const form = document.getElementById("productForm");
const grid = document.getElementById("productsGrid");
const imageInput = document.getElementById("imageInput");
const previewContainer = document.getElementById("previewContainer");
const descriptionTextarea = document.getElementById("editor");
const uploadZone = document.querySelector(".upload-zone");
const selectedImageCount = document.getElementById("imageCount");
const totalListings = document.getElementById("totalListings");
const activeListings = document.getElementById("activeListings");
const catalogValue = document.getElementById("catalogValue");
const inventoryCount = document.getElementById("inventoryCount");
const inventoryHeadingCount = document.getElementById("inventoryHeadingCount");
const categoryFilters = document.querySelectorAll("[data-category-filter]");
const studioTabs = document.querySelectorAll("[data-scroll-target]");
const draftButton = document.querySelector("[data-draft-button]");
const shopperPreviewImage = document.getElementById("shopperPreviewImage");
const shopperPreviewTitle = document.getElementById("shopperPreviewTitle");
const shopperPreviewCategory = document.getElementById(
  "shopperPreviewCategory",
);
const shopperPreviewPrice = document.getElementById("shopperPreviewPrice");
let descriptionEditor = null;
let selectedCategory = "All";
let loadedProducts = [];
let productsLoadError = false;

function updateShopperPreview() {
  if (shopperPreviewTitle)
    shopperPreviewTitle.textContent =
      form.elements.namedItem("title")?.value.trim() || "Your product title";
  if (shopperPreviewCategory)
    shopperPreviewCategory.textContent =
      form.elements.namedItem("category")?.value || "Select a category";
  if (shopperPreviewPrice)
    shopperPreviewPrice.textContent = `$${Number(form.elements.namedItem("price")?.value || 0).toFixed(2)}`;
}

["title", "category", "price"].forEach((fieldName) => {
  form.elements
    .namedItem(fieldName)
    ?.addEventListener("input", updateShopperPreview);
  form.elements
    .namedItem(fieldName)
    ?.addEventListener("change", updateShopperPreview);
});

// checks whether Only continue if CKEditor exists AND the textarea exists.
if (window.ClassicEditor && descriptionTextarea) {
  // This tells CKEditor: Take this textarea and turn it into a ClassicEditor.
  ClassicEditor.create(descriptionTextarea)
    .then((editor) => {
      // Save the editor
      descriptionEditor = editor;
    })
    .catch((error) => console.error(error));
}

function normalizeImagePath(imagePath) {
  if (
    !imagePath ||
    imagePath.startsWith("http") ||
    imagePath.startsWith("data:")
  ) {
    return imagePath;
  }

  return imagePath
    .replace(/^uploads\//, "../assets/uploads/")
    .replace(/^assets\/uploads\//, "../assets/uploads/");
}

// IMAGE PREVIEW before uploading them
imageInput.addEventListener("change", function () {
  // this refers to the file input element (example: user when upload the photo in FORM) Array.from() converts the FileList into a normal JavaScript array.
  const files = Array.from(this.files);

  if (files.length > 5) {
    setFieldError(null, "productImagesError", "You can upload a maximum of 5 images.");
    if (uploadZone) uploadZone.classList.add("is-invalid");
    this.value = "";
    return;
  }
  setFieldError(null, "productImagesError", "");
  if (uploadZone) uploadZone.classList.remove("is-invalid");

  previewContainer.innerHTML = "";
  if (selectedImageCount) {
    selectedImageCount.textContent = `${files.length} / 5 slots filled`;
  }

  // This is used to preview images that a user has selected from a file input
  files.forEach((file) => {
    // FileReader() is a browser API that allows JavaScript to read the contents of a file selected by the user.
    const reader = new FileReader();

    // onload tells browser When you finish reading the file, run this function.
    reader.onload = function (e) {
      const img = document.createElement("img");

      img.src = e.target.result;
      img.alt = file.name;

      previewContainer.appendChild(img);
      if (shopperPreviewImage && file === files[0])
        shopperPreviewImage.src = e.target.result;
    };

    reader.readAsDataURL(file);
  });
  // console.log(files); file contains users photos they choose to upload in web and to sell
});

updateShopperPreview();

if (uploadZone) {
  ["dragenter", "dragover"].forEach((eventName) => {
    uploadZone.addEventListener(eventName, (event) => {
      event.preventDefault();
      uploadZone.classList.add("is-dragging");
    });
  });

  ["dragleave", "drop"].forEach((eventName) => {
    uploadZone.addEventListener(eventName, (event) => {
      event.preventDefault();
      uploadZone.classList.remove("is-dragging");
    });
  });

  uploadZone.addEventListener("drop", (event) => {
    const files = Array.from(event.dataTransfer.files).filter((file) =>
      file.type.startsWith("image/"),
    );
    if (!files.length) return;

    const transfer = new DataTransfer();
    files.slice(0, 5).forEach((file) => transfer.items.add(file));
    imageInput.files = transfer.files;
    imageInput.dispatchEvent(new Event("change", { bubbles: true }));
  });
}

categoryFilters.forEach((filter) => {
  filter.addEventListener("click", () => {
    selectedCategory = filter.dataset.categoryFilter;
    categoryFilters.forEach((item) =>
      item.classList.toggle("is-active", item === filter),
    );
    renderLoadedProducts();
  });
});

studioTabs.forEach((tab) => {
  tab.addEventListener("click", () => {
    document
      .getElementById(tab.dataset.scrollTarget)
      ?.scrollIntoView({ behavior: "smooth", block: "start" });
    studioTabs.forEach((item) =>
      item.classList.toggle("is-active", item === tab),
    );
  });
});

if (draftButton) {
  draftButton.addEventListener("click", () => {
    const status = form.elements.namedItem("status");
    status.value = "draft";
    form.requestSubmit();
  });
}

// This function is taking the image value coming from your backend/database and making sure
// your JavaScript always gets a clean array of image URLs that it can use to display product images.
function getProductImages(imageValue) {
  if (!imageValue) return ["https://placehold.co/600x400?text=Product"];

  try {
    // JSON.parse() converts the JSON string into a JavaScript value. Now JavaScript can work with it as an array.
    const parsedImages = JSON.parse(imageValue);

    // Check if parsing produced an array
    if (Array.isArray(parsedImages) && parsedImages.length > 0) {
      // normalizeImagePath() function direction image value to the path we decide to go in repository
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

async function readApiResponse(response) {
  const responseText = await response.text();

  try {
    return JSON.parse(responseText);
  } catch (error) {
    return {
      success: false,
      message:
        responseText.trim() || "The server returned an unreadable response.",
    };
  }
}

function getProductsFromResponse(payload) {
  if (Array.isArray(payload)) return payload;
  if (payload && Array.isArray(payload.data)) return payload.data;
  return [];
}

// LOAD PRODUCTS
async function loadProducts() {
  const response = await fetch("../api/fetch_products.php?scope=mine");
  const payload = await readApiResponse(response);
  const products = getProductsFromResponse(payload);
  loadedProducts = products;
  productsLoadError = !response.ok;

  const activeProductCount = products.filter(
    (product) => (product.status || "active") === "active",
  ).length;
  const totalCatalogValue = products.reduce(
    (sum, product) => sum + Number(product.price || 0),
    0,
  );
  if (totalListings) totalListings.textContent = products.length;
  if (activeListings) activeListings.textContent = activeProductCount;
  if (catalogValue)
    catalogValue.textContent = `$${totalCatalogValue.toLocaleString(undefined, { maximumFractionDigits: 0 })}`;
  if (inventoryCount) inventoryCount.textContent = products.length;
  if (inventoryHeadingCount)
    inventoryHeadingCount.textContent = products.length;

  renderLoadedProducts();
}

function renderLoadedProducts() {
  const products =
    selectedCategory === "All"
      ? loadedProducts
      : loadedProducts.filter(
          (product) => product.category === selectedCategory,
        );

  grid.innerHTML = "";

  if (productsLoadError) {
    grid.innerHTML = `
            <div class="empty-state">
                <h2>Could not load products</h2>
                <p>Please try again.</p>
            </div>
        `;
    return;
  }

  if (products.length === 0) {
    grid.innerHTML = `
            <div class="empty-state">
                <h2>No products listed yet</h2>
                <p>Start selling your first product.</p>
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
function setFieldError(control, errorElementId, message) {
  const errorElement = document.getElementById(errorElementId);
  if (errorElement) errorElement.textContent = message;
  if (control) control.classList.toggle("is-invalid", Boolean(message));
  if (control && message) control.setAttribute("aria-invalid", "true");
  if (control && !message) control.removeAttribute("aria-invalid");
}

function clearProductFormErrors() {
  form.querySelectorAll(".is-invalid").forEach((element) => {
    element.classList.remove("is-invalid");
    element.removeAttribute("aria-invalid");
  });
  form.querySelectorAll(".field-error").forEach((element) => {
    element.textContent = "";
  });

  const formMessage = document.getElementById("productFormMessage");
  formMessage.classList.add("d-none");
  formMessage.classList.remove("alert-success");
  formMessage.classList.add("alert-danger");
  formMessage.textContent = "";
}

function showProductFormError(message, isSuccess = false) {
  const formMessage = document.getElementById("productFormMessage");
  formMessage.textContent = message;
  formMessage.classList.remove("d-none");
  formMessage.classList.toggle("alert-danger", !isSuccess);
  formMessage.classList.toggle("alert-success", isSuccess);
  formMessage.scrollIntoView({ behavior: "smooth", block: "nearest" });
}

form.addEventListener("submit", async function (e) {
  e.preventDefault();
  clearProductFormErrors();

  const errors = [];
  const title = form.elements.namedItem("title");
  const category = form.elements.namedItem("category");
  const price = form.elements.namedItem("price");
  const status = form.elements.namedItem("status");
  const phone = form.elements.namedItem("phone");

  if (!title.value.trim()) {
    setFieldError(title, "productTitleError", "Please enter a product title.");
    errors.push("Product title is required.");
  }
  if (!category.value) {
    setFieldError(category, "productCategoryError", "Please choose a category.");
    errors.push("Product category is required.");
  }
  if (!price.value || Number(price.value) < 0 || Number(price.value) > 1000000) {
    setFieldError(price, "productPriceError", "Enter a price between 0 and 1,000,000.");
    errors.push("Price is invalid.");
  }
  if (!status.value) {
    setFieldError(status, "productStatusError", "Please choose a listing status.");
    errors.push("Listing status is required.");
  }
  if (!/^[0-9+()\-\s]{6,30}$/.test(phone.value.trim())) {
    setFieldError(phone, "productPhoneError", "Enter 6 to 30 phone-safe characters.");
    errors.push("Phone number is invalid.");
  }

  if (imageInput.files.length < 1 || imageInput.files.length > 5) {
    setFieldError(null, "productImagesError", "Please choose between 1 and 5 product images.");
    if (uploadZone) uploadZone.classList.add("is-invalid");
    errors.push("Product images are required.");
  } else {
    const invalidImage = Array.from(imageInput.files).find((file) =>
      !["image/jpeg", "image/png", "image/gif", "image/webp"].includes(file.type) ||
      file.size < 1 ||
      file.size > 5242880
    );
    if (invalidImage) {
      setFieldError(null, "productImagesError", "Use JPG, PNG, GIF, or WEBP images up to 5MB each.");
      if (uploadZone) uploadZone.classList.add("is-invalid");
      errors.push("One or more images are invalid.");
    }
  }

  const descriptionHtml = getDescriptionHtml();

  if (!getDescriptionText(descriptionHtml)) {
    setFieldError(descriptionTextarea, "productDescriptionError", "Please enter a product description.");
    errors.push("Product description is required.");
  }

  if (errors.length) {
    showProductFormError(`Please fix the form: ${errors.join(" ")}`);
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

  const result = await readApiResponse(response);

  if (!response.ok) {
    const serverErrors = Array.isArray(result.errors) ? result.errors.join(" ") : "";
    showProductFormError(`${result.message || "Product request failed."}${serverErrors ? ` ${serverErrors}` : ""}`);
    return;
  }

  form.reset();
  if (descriptionEditor) {
    descriptionEditor.setData("");
  }

  previewContainer.innerHTML = "";
  if (selectedImageCount) selectedImageCount.textContent = "0 / 5 slots filled";
  showProductFormError(result.message || "Product saved successfully.", true);

  loadProducts();
});

loadProducts();
