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

  // It is a custom function that created to convert the date coming from your news API into a nicer format for the user.
  function formatWorldNewsDate(dateValue) {
    if (!dateValue) return "";

    const date = new Date(dateValue);

    if (Number.isNaN(date.getTime())) {
      return dateValue;
    }

    return date.toLocaleDateString(undefined, {
      year: "numeric",
      month: "short",
      day: "numeric",
    });
  }

  fetch("../api/api.php")
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

  function escapeHtml(value) {
    return String(value ?? "")
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  function cardProduct(product) {
    const title = escapeHtml(product.title || "Untitled article");
    const description = escapeHtml(product.description || "");
    const author = escapeHtml(product.author || "Unknown author");
    const image = escapeHtml(product.urlToImage || "");
    const url = escapeHtml(product.url || "#");
    const source = escapeHtml(product.source?.name || "World News");

    return `
<div class="col">
    <article class="news-card">

        <a
            href="${url}"
            target="_blank"
            rel="noopener noreferrer"
            class="news-card-link"
            aria-label="Read ${title}"
        >

            <div class="news-image-wrapper">
                <img
                    src="${image}"
                    class="news-card-image"
                    alt="${title}"
                    loading="lazy"
                >

                <span class="news-source-badge">
                    ${source || "World News"}
                </span>
            </div>

            <div class="news-card-body">

                <div class="news-meta">
                    <span>
                        ${author || "Unknown author"}
                    </span>

                    <span>•</span>

                    <span>
                        ${formatWorldNewsDate(product.publishedAt)}
                    </span>
                </div>

                <h3 class="news-card-title">
                    ${title || "Untitled article"}
                </h3>

                <p class="news-card-description">
                    ${description || "No description available."}
                </p>

                <span class="news-read-more">
                    Read article
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                </span>

            </div>

        </a>

    </article>
</div>
                        `;
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
});
