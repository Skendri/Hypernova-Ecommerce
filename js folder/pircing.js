document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("productForm");
  const grid = document.getElementById("productsGrid");
  let products = JSON.parse(sessionStorage.getItem("products") || "[]");

  // Render products
  function renderProducts() {
    grid.innerHTML = "";
    products.forEach((product, index) => {
      const col = document.createElement("div");
      col.className = "col";
      col.innerHTML = `
                <div class="product-card h-100">
                    <div id="productCarousel${index}" class="carousel slide h-100" data-bs-ride="carousel">
                        <div class="carousel-inner h-100">
                            ${product.images
                              .map(
                                (img, i) => `
                                <div class="carousel-item ${i === 0 ? "active" : ""}">
                                    <img src="${img}" class="d-block w-100 product-img" alt="Product image">
                                </div>
                            `,
                              )
                              .join("")}
                        </div>
                        ${
                          product.images.length > 1
                            ? `
                        <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel${index}" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#productCarousel${index}" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>`
                            : ""
                        }
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">${product.title}</h5>
                        <p class="card-text">${product.description}</p>
                        <small class="text-muted">Posted: ${product.date}</small>
                        <button class="btn btn-danger btn-sm mt-2 delete-btn" data-index="${index}">Delete</button>
                    </div>
                </div>
            `;
      grid.appendChild(col);
    });

    // Add delete listeners
    document.querySelectorAll(".delete-btn").forEach((btn) => {
      btn.addEventListener("click", function () {
        const index = parseInt(this.dataset.index);
        products.splice(index, 1);
        sessionStorage.setItem("products", JSON.stringify(products));
        renderProducts();
      });
    });
  }

  // Form submit
  form.addEventListener("submit", function (e) {
    e.preventDefault();
    const title = document.getElementById("title").value.trim();
    const description = document.getElementById("description").value.trim();
    const files = document.getElementById("images").files;

    if (!title || description.length < 10) {
      alert("Title required, description min 10 chars");
      return;
    }
    if (files.length === 0 || files.length > 5) {
      alert("1-5 images required");
      return;
    }

    const imagePromises = Array.from(files).map((file) => {
      return new Promise((resolve, reject) => {
        if (file.size > 2 * 1024 * 1024) {
          reject("Image too large");
          return;
        }
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result);
        reader.onerror = reject;
        reader.readAsDataURL(file);
      });
    });

    Promise.all(imagePromises)
      .then((images) => {
        const product = {
          id: Date.now() + Math.random(),
          title,
          description,
          date: new Date().toLocaleString(),
          images,
        };
        products.push(product);
        sessionStorage.setItem("products", JSON.stringify(products));
        form.reset();
        renderProducts();
        alert("Product listed successfully!");
      })
      .catch((err) => alert("Error: " + err));
  });

  // Initial render
  renderProducts();
});
