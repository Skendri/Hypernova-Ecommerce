// news fetching from newsapi.org
let allArticles = [];
let startIndex = 30;

function renderArticles(articlesToRender, isAppend = false) {
  const container = document.getElementById("news-container");
  let row;
  if (isAppend) {
    row = container.querySelector(".row");
  } else {
    let html = '<h3 class="p-4 mt-5">Latest Apple News</h3><div class="row">';
    container.innerHTML = html;
    row = container.querySelector(".row");
  }

  articlesToRender.forEach((article) => {
    const image = article.urlToImage
      ? `<img src="${article.urlToImage}" class="card-img-top" alt="${article.title}" style="height: 200px; object-fit: cover;">`
      : '<div class="card-img-top bg-secondary" style="height: 200px;"></div>';
    const colHtml = `
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
    row.insertAdjacentHTML("beforeend", colHtml);
  });

  if (!isAppend) {
    const loadMoreBtn = document.createElement("button");
    loadMoreBtn.id = "load-more-btn";
    loadMoreBtn.className = "btn btn-success m-4";
    loadMoreBtn.textContent = "Load More";
    loadMoreBtn.addEventListener("click", loadMoreArticles);
    container.appendChild(loadMoreBtn);
  }
}

function loadMoreArticles() {
  const nextArticles = allArticles.slice(startIndex, startIndex + 10);
  if (nextArticles.length === 0) {
    document.getElementById("load-more-btn").style.display = "none";
    return;
  }
  renderArticles(nextArticles, true);
  startIndex += 10;
}

fetch("./api/newsApi.php")
  .then((response) => response.json())
  .then((data) => {
    console.log(data);
    allArticles = data.articles;
    const initialArticles = allArticles.slice(0, 30);
    renderArticles(initialArticles);
  })
  .catch((error) => {
    console.error("Error fetching news:", error);
    document.getElementById("news-container").innerHTML =
      '<p class="p-4">Error loading news.</p>';
  });
// news fetching from newsapi.org
