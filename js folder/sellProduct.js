const form = document.getElementById("productForm");
const grid = document.getElementById("productsGrid");
const imageInput = document.getElementById("imageInput");
const previewContainer = document.getElementById("previewContainer");

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
      return parsedImages;
    }
  } catch (error) {
    return [imageValue];
  }

  return [imageValue];
}

// LOAD PRODUCTS
async function loadProducts() {
  const response = await fetch("./api/fetch_products.php");

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
    const thumbnails = productImages
      .slice(1, 5)
      .map(
        (image) => `
          <img src="${image}" class="product-thumb" alt="${product.title}">
        `,
      )
      .join("");

    grid.innerHTML += `

            <div class="col-lg-3 col-md-6">

                <a class="product-detail-link" href="./productView.php?id=${product.id}" aria-label="View ${product.title}">
                <div class="product-card h-100">

                    <img src="${mainImage}" class="product-img" alt="${product.title}">

                    ${
                      thumbnails
                        ? `<div class="product-thumbs">${thumbnails}</div>`
                        : ""
                    }

                    <div class="product-body d-flex flex-column justify-content-between h-100">

                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="category-badge">
                                    ${product.category}
                                </span>

                                <span class="price-badge">
                                    $${product.price}
                                </span>
                            </div>

                            <h5 class="product-title">
                                ${product.title}
                            </h5>

                            <p class="product-description">
                                ${product.description}
                            </p>
                        </div>

                        <div>
                            <small class="text-muted">
                                Posted ${product.created_at}
                            </small>
                        </div>

                    </div>

                </div>
                </a>

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

  const formData = new FormData(form);

  const response = await fetch("save_product.php", {
    method: "POST",
    body: formData,
  });

  const result = await response.text();

  alert(result);

  if (!response.ok) {
    return;
  }

  form.reset();

  previewContainer.innerHTML = "";

  loadProducts();
});

loadProducts();
