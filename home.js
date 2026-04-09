document.addEventListener("DOMContentLoaded", function () {
  // API per e-eccommerce products
  const containerItems = document.querySelector(".wrapper");
  let LoadMoreButton = document.getElementById("load-more");

  // sa items do te shfaqen ne fillim
  let initialItems = 8;
  // sa items jane gjithsej ne total te futu brenda arrayt
  let loadItems = [];

  fetch(
    "https://newsapi.org/v2/top-headlines?country=us&category=business&apiKey=8a66f5bb93ba47a7ad510af10a338c53",
  )
    .then((response) => response.json())
    .then((products) => {
      loadItems = products.articles;
      console.log(loadItems);
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

  // news fetching from newsapi.org
  fetch(
    "https://newsapi.org/v2/everything?q=apple&from=2026-04-03&to=2026-04-03&sortBy=popularity&apiKey=8a66f5bb93ba47a7ad510af10a338c53",
  )
    .then((response) => response.json())
    .then((data) => {
      console.log(data);
      const container = document.getElementById("news-container");
      const articles = data.articles.slice(0, 30); // Show top 30 articles
      let html = '<h3 class="p-4 mt-5">Latest Apple News</h3><div class="row">';
      articles.forEach((article) => {
        const image = article.urlToImage
          ? `<img src="${article.urlToImage}" class="card-img-top" alt="${article.title}" style="height: 200px; object-fit: cover;">`
          : '<div class="card-img-top bg-secondary" style="height: 200px;"></div>';
        html += `
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    ${image}
                                    <div class="card-body">
                                        <h5 class="card-title">${article.title}</h5>
                                        <p class="card-text">${article.description ? article.description.substring(0, 150) + "..." : "No description available."}</p>
                                        <a href="${article.url}" target="_blank" class="btn btn-primary">Read More</a>
                                    </div>
                                </div>
                            </div>
                        `;
      });
      html += "</div>";
      container.innerHTML = html;
    })
    .catch((error) => {
      console.error("Error fetching news:", error);
      document.getElementById("news-container").innerHTML =
        '<p class="p-4">Error loading news.</p>';
    });
  // news fetching from newsapi.org
}); // DOMContentLoaded event listener
