document.addEventListener("DOMContentLoaded", function () {
  const userProducts = document.getElementById("user-products");

  function createTextElement(tagName, className, text) {
    const element = document.createElement(tagName);
    element.className = className;
    element.textContent = text;
    return element;
  }

  function getProductImage(imageValue) {
    if (!imageValue) return "https://placehold.co/600x400?text=Product";

    try {
      const parsedImages = JSON.parse(imageValue);

      if (Array.isArray(parsedImages) && parsedImages.length > 0) {
        return parsedImages[0];
      }
    } catch (error) {
      return imageValue;
    }

    return imageValue;
  }

  function createUploadedProductCard(product) {
    const col = document.createElement("div");
    col.className = "col-lg-3 col-md-6";

    const card = document.createElement("div");
    card.className = "uploaded-product-card h-100";

    const image = document.createElement("img");
    image.className = "uploaded-product-img";
    image.src = getProductImage(product.image);
    image.alt = product.title || "Product image";

    const body = document.createElement("div");
    body.className = "uploaded-product-body";

    const meta = document.createElement("div");
    meta.className =
      "d-flex justify-content-between align-items-center gap-2 mb-3";

    meta.appendChild(
      createTextElement(
        "span",
        "uploaded-category-badge",
        product.category || "Product",
      ),
    );
    meta.appendChild(
      createTextElement(
        "span",
        "uploaded-price-badge",
        `$${product.price || "0.00"}`,
      ),
    );

    body.appendChild(meta);
    body.appendChild(
      createTextElement(
        "h5",
        "uploaded-product-title",
        product.title || "Untitled",
      ),
    );
    body.appendChild(
      createTextElement(
        "p",
        "uploaded-product-description",
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
        createTextElement(
          "small",
          "text-muted",
          `Posted ${product.created_at}`,
        ),
      );
    }

    card.appendChild(image);
    card.appendChild(body);

    const link = document.createElement("a");
    link.className = "uploaded-product-link";
    link.href = `./productView.php?id=${product.id}`;
    link.setAttribute("aria-label", `View ${product.title || "product"}`);
    link.appendChild(card);

    col.appendChild(link);

    return col;
  }

  async function loadUploadedProducts() {
    if (!userProducts) return;

    try {
      const response = await fetch("./api/fetch_products.php");
      const products = await response.json();

      userProducts.innerHTML = "";

      if (!Array.isArray(products) || products.length === 0) {
        userProducts.innerHTML = `
          <div class="col-12">
            <div class="uploaded-empty-state">No uploaded products yet.</div>
          </div>
        `;
        return;
      }

      products.forEach((product) => {
        userProducts.appendChild(createUploadedProductCard(product));
      });
    } catch (error) {
      console.error("Error loading uploaded products:", error);
      userProducts.innerHTML = `
        <div class="col-12">
          <div class="uploaded-empty-state">Could not load uploaded products.</div>
        </div>
      `;
    }
  }

  loadUploadedProducts();

  // API per e-eccommerce products
  const containerItems = document.querySelector(".wrapper");
  let LoadMoreButton = document.getElementById("load-more");

  // sa items do te shfaqen ne fillim
  let initialItems = 8;
  // sa items jane gjithsej ne total te futu brenda arrayt
  let loadItems = [];

  // Function to validate article (check for errors like missing image/text/desc)
  function isValidArticle(product) {
    return (
      product.urlToImage &&
      product.urlToImage.trim() !== "" &&
      product.title &&
      product.title.trim() !== "" &&
      product.description &&
      product.description.trim() !== ""
    );
  }

  fetch("./api/api.php")
    .then((response) => response.json())
    .then((products) => {
      // Filter out invalid articles (errors)
      console.log("Fetched products:", products.articles);
      loadItems = products.articles.filter(isValidArticle);
      console.log("Total articles fetched:", products.articles.length);
      console.log(
        `Filtered ${loadItems.length} valid articles out of ${products.articles.length}`,
      );
      // console.log(loadItems);
      // Shfaqim produktet e para
      renderInitial();
    })
    .catch((error) => {
      console.error("Error fetching data:", error);
    });

  function cardProduct(product) {
    return `
                     <div class="col" id="${product.source.id}">
                            <a class="p-3" href="${product.url}" target="_blank">
                                <div class="card" style="width: 18rem;">
                                    <img src="${product.urlToImage}" class="card-img-top" alt="">
                                    <div class="card-body">
                                        <h5 class="card-title">${product.title}</h5>
                                        <p class="card-text">${product.description}</p>
                                    </div>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item">${product.author}</li>
                                            <li class="list-group-item">${product.publishedAt}</li>
                                            <li class="list-group-item">Example</li>
                                        </ul>
                                        <div class="card-body">
                                            <a href="#" class="card-link">${product.content}</a>
                                            <a href="#" class="card-link">${product.source.name}</a>
                                        </div>
                                </div>
                            </a>
                        </div>`;
  }

  // 3. RENDER FILLIMI (8 produktet e para)
  function renderInitial() {
    const firstItems = loadItems.slice(0, initialItems);

    firstItems.forEach((product) => {
      containerItems.innerHTML += cardProduct(product);
    });
  }
  // API per e-eccommerce products

  // 4. LOAD MORE (shton vetem te rinjte)
  function loadMore() {
    let start = initialItems;
    let end = initialItems + 8;

    let nextItems = loadItems.slice(start, end);

    nextItems.forEach((product) => {
      containerItems.innerHTML += cardProduct(product);
    });

    initialItems = end;

    // fsheh butonin kur mbarojne produktet
    if (initialItems >= loadItems.length) {
      LoadMoreButton.style.display = "none";
    }
  }

  // 5. EVENT BUTTON
  LoadMoreButton.addEventListener("click", loadMore);
}); // DOMContentLoaded event listener
