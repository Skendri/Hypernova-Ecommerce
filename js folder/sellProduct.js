const form = document.getElementById("productForm");
const grid = document.getElementById("productsGrid");
const imageInput = document.getElementById("imageInput");
const previewContainer = document.getElementById("previewContainer");

// IMAGE PREVIEW
imageInput.addEventListener("change", function () {
  previewContainer.innerHTML = "";

  const file = this.files[0];

  if (!file) return;

  const reader = new FileReader();

  reader.onload = function (e) {
    const img = document.createElement("img");

    img.src = e.target.result;

    previewContainer.appendChild(img);
  };

  reader.readAsDataURL(file);
});

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
    grid.innerHTML += `

            <div class="col-lg-3 col-md-6">

                <div class="product-card h-100">

                    <img src="${product.image}" class="product-img">

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

            </div>
        `;
  });
}

// FORM SUBMIT
form.addEventListener("submit", async function (e) {
  e.preventDefault();

  const formData = new FormData(form);

  const response = await fetch("save_product.php", {
    method: "POST",
    body: formData,
  });

  const result = await response.text();

  alert(result);

  form.reset();

  previewContainer.innerHTML = "";

  loadProducts();
});

loadProducts();
