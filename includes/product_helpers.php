<?php

const PRODUCT_CATEGORIES = ['Electronics', 'Fashion', 'Gaming', 'Home', 'Sports'];
const PRODUCT_STATUSES = ['draft', 'active', 'sold', 'hidden'];
const PRODUCT_MAX_IMAGES = 5;
const PRODUCT_MAX_IMAGE_BYTES = 5242880;
const PRODUCT_DESCRIPTION_ALLOWED_TAGS = [
    'a',
    'b',
    'br',
    'em',
    'h2',
    'h3',
    'h4',
    'i',
    'li',
    'ol',
    'p',
    'strong',
    'ul',
];

function ensure_products_schema(mysqli $db): void
{
    ensure_products_column(
        $db,
        'status',
        "ALTER TABLE products ADD COLUMN status ENUM('draft', 'active', 'sold', 'hidden') NOT NULL DEFAULT 'active'"
    );
    ensure_products_column(
        $db,
        'created_at',
        "ALTER TABLE products ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP"
    );
    ensure_products_column(
        $db,
        'updated_at',
        "ALTER TABLE products ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
    );
}

function ensure_products_column(mysqli $db, string $columnName, string $alterSql): void
{
    $stmt = $db->prepare(
        "SELECT COUNT(*) AS column_count
         FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME = 'products'
           AND COLUMN_NAME = ?"
    );

    if (!$stmt) {
        throw new RuntimeException('Could not inspect products table.');
    }

    $stmt->bind_param('s', $columnName);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if ((int) ($row['column_count'] ?? 0) === 0 && !$db->query($alterSql)) {
        throw new RuntimeException('Could not update products table: ' . $db->error);
    }
}

function validate_product_input(bool $requireImages): array
{
    $title = trim_post_value('title');
    $description = trim_post_value('description');
    $priceValue = trim_post_value('price');
    $category = trim_post_value('category');
    $phone = trim_post_value('phone');
    $status = trim_post_value('status') ?: 'active';
    $errors = [];

    if ($title === '' || product_text_length($title) > 120) {
        $errors[] = 'Product title is required and must be 120 characters or fewer.';
    }

    $descriptionText = trim(strip_tags($description));
    if ($descriptionText === '' || product_text_length($descriptionText) > 3000) {
        $errors[] = 'Description is required and must be 3000 characters or fewer.';
    }

    if ($priceValue === '' || !is_numeric($priceValue) || (float) $priceValue < 0 || (float) $priceValue > 1000000) {
        $errors[] = 'Price must be a valid number between 0 and 1,000,000.';
    }

    if (!in_array($category, PRODUCT_CATEGORIES, true)) {
        $errors[] = 'Please choose a valid product category.';
    }

    if ($phone === '' || !preg_match('/^[0-9+()\-\s]{6,30}$/', $phone)) {
        $errors[] = 'Phone number must be 6 to 30 characters and contain only phone-safe characters.';
    }

    if (!in_array($status, PRODUCT_STATUSES, true)) {
        $errors[] = 'Please choose a valid listing status.';
    }

    if ($requireImages && get_upload_image_count() < 1) {
        $errors[] = 'Please upload at least one product image.';
    }

    if (!empty($errors)) {
        send_json(422, [
            'success' => false,
            'message' => 'Please fix the product form.',
            'errors' => $errors,
        ]);
    }

    return [
        'title' => $title,
        'description' => sanitize_product_description($description),
        'price' => (float) $priceValue,
        'category' => $category,
        'phone' => $phone,
        'status' => $status,
    ];
}

function get_upload_image_count(): int
{
    return count(get_upload_image_indexes());
}

function save_uploaded_product_images(): array
{
    $imageIndexes = get_upload_image_indexes();
    $imageCount = count($imageIndexes);

    if ($imageCount === 0) {
        return [];
    }

    if ($imageCount > PRODUCT_MAX_IMAGES) {
        send_json(422, [
            'success' => false,
            'message' => 'Please upload between 1 and 5 images.',
        ]);
    }

    $uploadDir = __DIR__ . '/../assets/uploads';

    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
        send_json(500, [
            'success' => false,
            'message' => 'Could not prepare the uploads folder.',
        ]);
    }

    $uploadedImages = [];
    $allowedMimeTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];
    $fileInfo = new finfo(FILEINFO_MIME_TYPE);

    foreach ($imageIndexes as $position => $fileIndex) {
        if ($_FILES['images']['error'][$fileIndex] !== UPLOAD_ERR_OK) {
            send_json(422, [
                'success' => false,
                'message' => 'One of the images could not be uploaded.',
            ]);
        }

        if ($_FILES['images']['size'][$fileIndex] < 1 || $_FILES['images']['size'][$fileIndex] > PRODUCT_MAX_IMAGE_BYTES) {
            send_json(422, [
                'success' => false,
                'message' => 'Each image must be 5MB or smaller.',
            ]);
        }

        $tmpName = $_FILES['images']['tmp_name'][$fileIndex];

        if (!is_uploaded_file($tmpName)) {
            send_json(422, [
                'success' => false,
                'message' => 'One of the uploaded files is invalid.',
            ]);
        }

        $mimeType = $fileInfo->file($tmpName);

        if (!isset($allowedMimeTypes[$mimeType])) {
            send_json(422, [
                'success' => false,
                'message' => 'Only JPG, PNG, GIF, and WEBP images are allowed.',
            ]);
        }

        $imageName = time() . '_' . $position . '_' . bin2hex(random_bytes(8)) . '.' . $allowedMimeTypes[$mimeType];
        $targetPath = $uploadDir . '/' . $imageName;

        if (!move_uploaded_file($tmpName, $targetPath)) {
            send_json(500, [
                'success' => false,
                'message' => 'Could not save one of the images.',
            ]);
        }

        $uploadedImages[] = '../assets/uploads/' . $imageName;
    }

    return $uploadedImages;
}

function decode_product_images(?string $imageJson): array
{
    if (!$imageJson) {
        return [];
    }

    $decoded = json_decode($imageJson, true);

    if (is_array($decoded)) {
        return array_values(array_filter($decoded, 'is_string'));
    }

    return [$imageJson];
}

function get_upload_image_indexes(): array
{
    if (!isset($_FILES['images']) || !is_array($_FILES['images']['name'])) {
        return [];
    }

    $indexes = [];

    foreach ($_FILES['images']['name'] as $index => $name) {
        if (trim((string) $name) !== '') {
            $indexes[] = $index;
        }
    }

    return $indexes;
}

function product_text_length(string $value): int
{
    return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
}

function sanitize_product_description(string $html): string
{
    $html = trim($html);

    if ($html === '') {
        return '';
    }

    if (!class_exists('DOMDocument')) {
        return htmlspecialchars(strip_tags($html), ENT_QUOTES, 'UTF-8');
    }

    $document = new DOMDocument();
    libxml_use_internal_errors(true);
    $document->loadHTML(
        '<!doctype html><html><body>' . $html . '</body></html>',
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );
    libxml_clear_errors();

    $body = $document->getElementsByTagName('body')->item(0);

    if (!$body) {
        return '';
    }

    sanitize_product_dom_node($body);

    $cleanHtml = '';

    foreach ($body->childNodes as $child) {
        $cleanHtml .= $document->saveHTML($child);
    }

    return trim($cleanHtml);
}

function sanitize_product_dom_node(DOMNode $node): void
{
    if (!$node->hasChildNodes()) {
        return;
    }

    for ($index = $node->childNodes->length - 1; $index >= 0; $index--) {
        $child = $node->childNodes->item($index);

        if (!$child) {
            continue;
        }

        if ($child instanceof DOMElement) {
            $tagName = strtolower($child->tagName);

            if (in_array($tagName, ['script', 'style'], true)) {
                $node->removeChild($child);
                continue;
            }

            if (!in_array($tagName, PRODUCT_DESCRIPTION_ALLOWED_TAGS, true)) {
                sanitize_product_dom_node($child);

                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }

                $node->removeChild($child);
                continue;
            }

            sanitize_product_element_attributes($child);
        }

        sanitize_product_dom_node($child);
    }
}

function sanitize_product_element_attributes(DOMElement $element): void
{
    $href = strtolower($element->tagName) === 'a' ? $element->getAttribute('href') : '';

    for ($index = $element->attributes->length - 1; $index >= 0; $index--) {
        $attribute = $element->attributes->item($index);

        if ($attribute instanceof DOMAttr) {
            $element->removeAttribute($attribute->nodeName);
        }
    }

    if (strtolower($element->tagName) !== 'a') {
        return;
    }

    if (!preg_match('/^(https?:|mailto:|tel:)/i', $href)) {
        $element->removeAttribute('href');
        return;
    }

    $element->setAttribute('href', $href);
    $element->setAttribute('target', '_blank');
    $element->setAttribute('rel', 'noopener noreferrer');
}

