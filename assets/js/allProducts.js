document.addEventListener("DOMContentLoaded", function () {
  const productsGrid = document.getElementById("all-products");

  function createTextElement(tagName, className, text) {
    const element = document.createElement(tagName);
    element.className = className;
    element.textContent = text;
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

    const link = document.createElement("a");
    link.className = "all-product-link";
    link.href = `productView.php?id=${product.id}`;
    link.setAttribute("aria-label", `View ${product.title || "product"}`);

    const card = document.createElement("article");
    card.className = "all-product-card h-100";

    const image = document.createElement("img");
    image.className = "all-product-img";
    image.src = getProductImage(product.image);
    image.alt = product.title || "Product image";

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
    body.appendChild(
      createTextElement("h2", "all-product-title", product.title || "Untitled"),
    );
    body.appendChild(
      createTextElement(
        "p",
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

    card.appendChild(image);
    card.appendChild(body);
    link.appendChild(card);
    col.appendChild(link);

    return col;
  }

  async function loadAllProducts() {
    if (!productsGrid) return;

    try {
      const response = await fetch("../api/fetch_products.php");
      const products = await response.json();

      productsGrid.innerHTML = "";

      if (!Array.isArray(products) || products.length === 0) {
        productsGrid.innerHTML = `
          <div class="col-12">
            <div class="all-products-empty">No uploaded products yet.</div>
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

  loadAllProducts();
});
