const form = document.getElementById("productForm");
const grid = document.getElementById("productsGrid");
const imageInput = document.getElementById("imageInput");
const previewContainer = document.getElementById("previewContainer");
const descriptionTextarea = document.getElementById("editor");
let descriptionEditor = null;

if (window.ClassicEditor && descriptionTextarea) {
  ClassicEditor.create(descriptionTextarea)
    .then((editor) => {
      descriptionEditor = editor;
    })
    .catch((error) => console.error(error));
}

function normalizeImagePath(imagePath) {
  if (!imagePath || imagePath.startsWith("http") || imagePath.startsWith("data:")) {
    return imagePath;
  }

  return imagePath
    .replace(/^uploads\//, "../assets/uploads/")
    .replace(/^assets\/uploads\//, "../assets/uploads/");
}

// IMAGE PREVIEW
imageInput.addEventListener("change", function () {
  previewContainer.innerHTML = "";

  const files = Array.from(this.files);

  if (files.length > 5) {
    alert("You can upload a maximum of 5 images.");
    this.value = "";
    return;
  }

  files.forEach((file) => {
    const reader = new FileReader();

    reader.onload = function (e) {
      const img = document.createElement("img");

      img.src = e.target.result;
      img.alt = file.name;

      previewContainer.appendChild(img);
    };

    reader.readAsDataURL(file);
  });
});

function getProductImages(imageValue) {
  if (!imageValue) return ["https://placehold.co/600x400?text=Product"];

  try {
    const parsedImages = JSON.parse(imageValue);

    if (Array.isArray(parsedImages) && parsedImages.length > 0) {
      return parsedImages.map(normalizeImagePath);
    }
  } catch (error) {
    return [normalizeImagePath(imageValue)];
  }

  return [normalizeImagePath(imageValue)];
}

function getDescriptionHtml() {
  if (descriptionEditor) {
    return descriptionEditor.getData();
  }

  return descriptionTextarea ? descriptionTextarea.value : "";
}

function getDescriptionText(html) {
  const container = document.createElement("div");
  container.innerHTML = html;
  return container.textContent.trim();
}

function sanitizeProductDescription(html) {
  const template = document.createElement("template");
  const allowedTags = new Set([
    "A",
    "B",
    "BR",
    "EM",
    "H2",
    "H3",
    "H4",
    "I",
    "LI",
    "OL",
    "P",
    "STRONG",
    "UL",
  ]);

  template.innerHTML = html || "";

  template.content.querySelectorAll("*").forEach((element) => {
    if (!allowedTags.has(element.tagName)) {
      element.replaceWith(...element.childNodes);
      return;
    }

    [...element.attributes].forEach((attribute) => {
      if (element.tagName !== "A" || attribute.name !== "href") {
        element.removeAttribute(attribute.name);
      }
    });

    if (element.tagName === "A") {
      const href = element.getAttribute("href") || "";
      const isSafeLink = /^(https?:|mailto:|tel:)/i.test(href);

      if (!isSafeLink) {
        element.replaceWith(...element.childNodes);
        return;
      }

      element.target = "_blank";
      element.rel = "noopener noreferrer";
    }
  });

  return template.innerHTML.trim();
}

function escapeHtml(value) {
  const container = document.createElement("div");
  container.textContent = value || "";
  return container.innerHTML;
}

function getProductUrl(product) {
  return `productView.php?id=${encodeURIComponent(product.id)}`;
}

function createDescriptionPreview(description) {
  const preview = document.createElement("div");
  preview.className = "product-description";
  preview.innerHTML = sanitizeProductDescription(description);
  return preview.outerHTML;
}

// LOAD PRODUCTS
async function loadProducts() {
  const response = await fetch("../api/fetch_products.php?scope=mine");
  const products = await response.json();

  grid.innerHTML = "";

  if (products.length === 0) {
    grid.innerHTML = `
            <div class="empty-state">
                <h2>No products listed yet</h2>
                <p>Start selling your first product</p>
            </div>
        `;

    return;
  }

  products.forEach((product) => {
    const productImages = getProductImages(product.image);
    const mainImage = productImages[0];
    const productUrl = getProductUrl(product);
    const productTitle = escapeHtml(product.title || "Untitled Product");
    const productCategory = escapeHtml(product.category || "Product");
    const productPhone = escapeHtml(product.phone || "");
    const productCreatedAt = escapeHtml(product.created_at || "");
    const productStatus = escapeHtml(product.status || "active");
    const thumbnails = productImages
      .slice(1, 5)
      .map(
        (image) => `
          <img src="${escapeHtml(image)}" class="product-thumb" alt="${productTitle}">
        `,
      )
      .join("");

    grid.innerHTML += `

            <div class="col-lg-3 col-md-6">

                <div class="product-card h-100">

                    <a class="product-detail-link" href="${productUrl}" aria-label="View ${productTitle}">
                        <img src="${escapeHtml(mainImage)}" class="product-img" alt="${productTitle}">
                    </a>

                    ${
                      thumbnails
                        ? `<div class="product-thumbs">${thumbnails}</div>`
                        : ""
                    }

                    <div class="product-body d-flex flex-column justify-content-between h-100">

                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="category-badge">
                                    ${productCategory}
                                </span>

                                <span class="price-badge">
                                    $${escapeHtml(product.price || "0.00")}
                                </span>
                            </div>

                            <small class="d-block text-muted mb-2">Status: ${productStatus}</small>

                            <a class="product-title-link" href="${productUrl}">
                                <h5 class="product-title">
                                    ${productTitle}
                                </h5>
                            </a>

                            ${createDescriptionPreview(product.description || "")}

                            ${
                              product.phone
                                ? `<small class="d-block text-muted mb-2">Phone: ${productPhone}</small>`
                                : ""
                            }
                        </div>

                        <div>
                            <small class="text-muted">
                                Posted ${productCreatedAt}
                            </small>
                            <div class="d-flex gap-2 mt-3">
                              <a class="btn btn-sm btn-outline-primary" href="editProduct.php?id=${encodeURIComponent(product.id)}">Edit</a>
                              <a class="btn btn-sm btn-outline-secondary" href="${productUrl}">View</a>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        `;
  });
}

// FORM SUBMIT
form.addEventListener("submit", async function (e) {
  e.preventDefault();

  if (imageInput.files.length < 1 || imageInput.files.length > 5) {
    alert("Please choose between 1 and 5 images.");
    return;
  }

  const descriptionHtml = getDescriptionHtml();

  if (!getDescriptionText(descriptionHtml)) {
    alert("Please enter a product description.");
    return;
  }

  if (descriptionEditor) {
    descriptionEditor.updateSourceElement();
  }

  const formData = new FormData(form);
  formData.set("description", descriptionHtml);

  const response = await fetch("../api/save_product.php", {
    method: "POST",
    body: formData,
  });

  const result = await response.json().catch(() => ({
    message: "The server returned an unreadable response.",
  }));

  alert(result.message || "Product request finished.");

  if (!response.ok) {
    return;
  }

  form.reset();
  if (descriptionEditor) {
    descriptionEditor.setData("");
  }

  previewContainer.innerHTML = "";

  loadProducts();
});

loadProducts();
