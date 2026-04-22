document.addEventListener("DOMContentLoaded", function () {
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
      loadItems = products.articles.filter(isValidArticle);
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
                            <div class="p-3">
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
                            </div>
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
