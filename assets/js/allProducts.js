document.addEventListener("DOMContentLoaded", function () {
  const productsGrid = document.getElementById("all-products");
  const filtersForm = document.getElementById("productFilters");
  const clearFiltersButton = document.getElementById("clearFilters");

  function createTextElement(tagName, className, text) {
    const element = document.createElement(tagName);
    element.className = className;
    element.textContent = text;
    return element;
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

  function createRichTextElement(tagName, className, html) {
    const element = document.createElement(tagName);
    element.className = className;
    element.innerHTML = sanitizeProductDescription(html);
    return element;
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

  function getProductImage(imageValue) {
    if (!imageValue) return "https://placehold.co/600x400?text=Product";

    try {
      const parsedImages = JSON.parse(imageValue);

      if (Array.isArray(parsedImages) && parsedImages.length > 0) {
        return normalizeImagePath(parsedImages[0]);
      }
    } catch (error) {
      return normalizeImagePath(imageValue);
    }

    return normalizeImagePath(imageValue);
  }

  function createProductCard(product) {
    const col = document.createElement("div");
    col.className = "col-xl-3 col-lg-4 col-md-6";

    const productUrl = `productView.php?id=${encodeURIComponent(product.id)}`;

    const card = document.createElement("article");
    card.className = "all-product-card h-100";

    const imageLink = document.createElement("a");
    imageLink.className = "all-product-link";
    imageLink.href = productUrl;
    imageLink.setAttribute("aria-label", `View ${product.title || "product"}`);

    const image = document.createElement("img");
    image.className = "all-product-img";
    image.src = getProductImage(product.image);
    image.alt = product.title || "Product image";
    imageLink.appendChild(image);

    const body = document.createElement("div");
    body.className = "all-product-body";

    const meta = document.createElement("div");
    meta.className =
      "d-flex justify-content-between align-items-center gap-2 mb-3";

    meta.appendChild(
      createTextElement(
        "span",
        "all-product-category",
        product.category || "Product",
      ),
    );
    meta.appendChild(
      createTextElement(
        "span",
        "all-product-price",
        `$${product.price || "0.00"}`,
      ),
    );

    body.appendChild(meta);
    const titleLink = document.createElement("a");
    titleLink.className = "all-product-title-link";
    titleLink.href = productUrl;
    titleLink.appendChild(
      createTextElement("h2", "all-product-title", product.title || "Untitled"),
    );

    body.appendChild(titleLink);
    body.appendChild(
      createRichTextElement(
        "div",
        "all-product-description",
        product.description || "",
      ),
    );
    body.appendChild(
      createTextElement(
        "small",
        "d-block text-muted mb-2",
        `Seller: ${product.owner_name || "Unknown user"}`,
      ),
    );

    if (product.phone) {
      body.appendChild(
        createTextElement(
          "small",
          "d-block text-muted mb-2",
          `Phone: ${product.phone}`,
        ),
      );
    }

    if (product.created_at) {
      body.appendChild(
        createTextElement("small", "text-muted", `Posted ${product.created_at}`),
      );
    }

    card.appendChild(imageLink);
    card.appendChild(body);
    col.appendChild(card);

    return col;
  }

  function getFilterParams() {
    const params = new URLSearchParams();

    if (!filtersForm) return params;

    const formData = new FormData(filtersForm);

    formData.forEach((value, key) => {
      const cleanValue = String(value).trim();

      if (cleanValue !== "") {
        params.append(key, cleanValue);
      }
    });

    return params;
  }

  function hasActiveFilters() {
    return getFilterParams().toString() !== "";
  }

  async function readApiResponse(response) {
    const responseText = await response.text();

    try {
      return JSON.parse(responseText);
    } catch (error) {
      return {
        success: false,
        message: responseText.trim() || "The server returned an unreadable response.",
      };
    }
  }

  function getProductsFromResponse(payload) {
    if (Array.isArray(payload)) return payload;
    if (payload && Array.isArray(payload.data)) return payload.data;
    return [];
  }

  async function loadAllProducts() {
    if (!productsGrid) return;

    try {
      const params = getFilterParams();
      const requestUrl = params.toString()
        ? `../api/fetch_products.php?${params.toString()}`
        : "../api/fetch_products.php";
      const response = await fetch(requestUrl);
      const payload = await readApiResponse(response);
      const products = getProductsFromResponse(payload);

      if (!response.ok) {
        throw new Error(payload.message || "Could not load products.");
      }

      productsGrid.innerHTML = "";

      if (!Array.isArray(products) || products.length === 0) {
        const emptyMessage = hasActiveFilters()
          ? "No products match your filters."
          : "No uploaded products yet.";

        productsGrid.innerHTML = `
          <div class="col-12">
            <div class="all-products-empty">${emptyMessage}</div>
          </div>
        `;
        return;
      }

      products.forEach((product) => {
        productsGrid.appendChild(createProductCard(product));
      });
    } catch (error) {
      console.error("Error loading all products:", error);
      productsGrid.innerHTML = `
        <div class="col-12">
          <div class="all-products-empty">Could not load products.</div>
        </div>
      `;
    }
  }

  if (filtersForm) {
    filtersForm.addEventListener("submit", function (event) {
      event.preventDefault();
      loadAllProducts();
    });
  }

  if (clearFiltersButton && filtersForm) {
    clearFiltersButton.addEventListener("click", function () {
      filtersForm.reset();
      loadAllProducts();
    });
  }

  loadAllProducts();
});
