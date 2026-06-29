document.addEventListener("DOMContentLoaded", function () {
  const userProducts = document.getElementById("user-products");
  const newsPost = document.getElementById("news-post");
  const apiAnotherPage = document.getElementById("api-anotherPage");
  const appleApi = document.getElementById("apple-api");

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
  // kjo eshte ne qoftese user harron te vendose foto kur krijon nje listing vendosen dy default nje random foto
  const fallbackImage =
    "https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=900&q=80";

  function normalizeImagePath(imagePath) {
    if (!imagePath) return fallbackImage;
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

  // Product created from users to sell in home page

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

  function createUploadedProductCard(product) {
    const col = document.createElement("div");
    col.className =
      "col-lg-3 col-md-6 animate__animated animate__delay-1s animate__fadeInDown";

    const card = document.createElement("div");
    card.className = "uploaded-product-card h-100";

    const productUrl = `productView.php?id=${encodeURIComponent(product.id)}`;

    const imageLink = document.createElement("a");
    imageLink.className = "uploaded-product-media-link";
    imageLink.href = productUrl;
    imageLink.setAttribute("aria-label", `View ${product.title || "product"}`);

    const image = document.createElement("img");
    image.className = "uploaded-product-img";
    image.src = getProductImage(product.image);
    image.alt = product.title || "Product image";
    imageLink.appendChild(image);

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
    const titleLink = document.createElement("a");
    titleLink.className = "uploaded-product-title-link";
    titleLink.href = productUrl;
    titleLink.appendChild(
      createTextElement("h5", "uploaded-product-title", product.title || "Untitled"),
    );

    body.appendChild(titleLink);
    body.appendChild(
      createRichTextElement(
        "div",
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

    card.appendChild(imageLink);
    card.appendChild(body);

    col.appendChild(card);

    return col;
  }

  async function loadUploadedProducts() {
    if (!userProducts) return;

    try {
      const response = await fetch("../api/fetch_products.php?limit=4");
      const payload = await readApiResponse(response);
      const products = Array.isArray(payload)
        ? payload
        : Array.isArray(payload.data)
          ? payload.data
          : [];

      if (!response.ok) {
        throw new Error(payload.message || "Could not load uploaded products.");
      }

      userProducts.innerHTML = "";

      if (!Array.isArray(products) || products.length === 0) {
        userProducts.innerHTML = `
          <div class="col-12">
            <div class="uploaded-empty-state">No uploaded products yet.</div>
          </div>
        `;
        return;
      }

      products.slice(0, 4).forEach((product) => {
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

  // END of Product created from users to sell in home page

  // the logic to display news that are created from users in pricing.php to displayed in home.php

  function getBlogImage(imagePath) {
    return (
      normalizeImagePath(imagePath) ||
      "https://placehold.co/900x520?text=Blog+Post"
    );
  }

  function formatBlogDate(dateValue) {
    if (!dateValue) return "";

    const date = new Date(dateValue.replace(" ", "T"));

    if (Number.isNaN(date.getTime())) {
      return dateValue;
    }

    return date.toLocaleDateString(undefined, {
      year: "numeric",
      month: "short",
      day: "numeric",
    });
  }

  function createBlogPostCard(post) {
    const link = document.createElement("a");
    link.className = "home-blog-link";
    link.href = `fullPost-page.php?id=${encodeURIComponent(post.id)}`;
    link.setAttribute("aria-label", `Read ${post.title || "blog post"}`);

    const card = document.createElement("article");
    card.className =
      "home-blog-card animate__animated animate__delay-1s animate__fadeInDown";

    const image = document.createElement("img");
    image.className = "home-blog-img";
    image.src = getBlogImage(post.cover_image);
    image.alt = post.title || "Blog post cover";

    const body = document.createElement("div");
    body.className = "home-blog-body";

    const meta = document.createElement("div");
    meta.className = "home-blog-meta";
    meta.appendChild(
      createTextElement(
        "span",
        "home-blog-author",
        post.author_name || "Unknown author",
      ),
    );

    if (post.created_at) {
      meta.appendChild(
        createTextElement(
          "span",
          "home-blog-date",
          formatBlogDate(post.created_at),
        ),
      );
    }

    body.appendChild(meta);
    body.appendChild(
      createTextElement("h4", "home-blog-title", post.title || "Untitled post"),
    );
    body.appendChild(
      createTextElement("p", "home-blog-excerpt", post.excerpt || ""),
    );

    card.appendChild(image);
    card.appendChild(body);

    link.appendChild(card);

    return link;
  }

  async function loadBlogPosts() {
    if (!newsPost) return;

    const blogGrid = document.getElementById("home-blog-grid");

    if (!blogGrid) return;

    try {
      const response = await fetch(
        "../api/fetch_blog_posts.php?scope=published&limit=4",
        {
          credentials: "same-origin",
        },
      );
      const posts = await readApiResponse(response);

      if (!response.ok) {
        throw new Error(posts.message || "Could not load blog posts.");
      }

      blogGrid.innerHTML = "";

      if (!Array.isArray(posts) || posts.length === 0) {
        blogGrid.innerHTML =
          '<div class="home-blog-empty">No published blog posts yet.</div>';
        return;
      }

      posts.slice(0, 4).forEach((post) => {
        blogGrid.appendChild(createBlogPostCard(post));
      });
    } catch (error) {
      console.error("Error loading blog posts:", error);
      blogGrid.innerHTML = `<div class="home-blog-empty">${error.message}</div>`;
    }
  }
  // the END of logic to display news that are created from users in pricing.php to displayed in home.php

  // function product to sell
  loadUploadedProducts();
  // function for blog created
  loadBlogPosts();
  // function for world news api preview
  loadWorldNewsPreview();
  // function for apple news api preview
  loadAppleNewsPreview();

  // reklamimi i 4 lajmeve te para nga bota
  function isValidWorldNewsArticle(article) {
    return (
      article.urlToImage &&
      article.urlToImage.trim() !== "" &&
      article.title &&
      article.title.trim() !== "" &&
      article.description &&
      article.description.trim() !== "" &&
      article.url &&
      article.url.trim() !== ""
    );
  }

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

  function createWorldNewsPreviewCard(article) {
    const card = document.createElement("article");
    card.className = "home-api-card";

    const link = document.createElement("a");
    link.className = "home-api-link";
    link.href = article.url;
    link.target = "_blank";
    link.rel = "noopener noreferrer";

    const image = document.createElement("img");
    image.className = "home-api-img";
    image.src = article.urlToImage;
    image.alt = article.title || "World news image";

    const body = document.createElement("div");
    body.className = "home-api-body";

    const source = article.source?.name || "World news";
    body.appendChild(createTextElement("span", "home-api-source", source));
    body.appendChild(
      createTextElement("h4", "home-api-title", article.title || "Untitled"),
    );
    body.appendChild(
      createTextElement("p", "home-api-description", article.description || ""),
    );

    if (article.publishedAt) {
      body.appendChild(
        createTextElement(
          "small",
          "home-api-date",
          formatWorldNewsDate(article.publishedAt),
        ),
      );
    }

    link.appendChild(image);
    link.appendChild(body);
    card.appendChild(link);

    return card;
  }

  async function loadWorldNewsPreview() {
    if (!apiAnotherPage) return;

    apiAnotherPage.innerHTML = `
      <div class="home-api-heading">
        <div class="my-2">
          <p class="home-api-eyebrow">World news</p>
          <h3>News from World</h3>
        </div>
        <a class="btn" style="background-color: #b45309; border-color: #b45309; color: #fff;" href="worldNews.php">See all news</a>
      </div>
      <div class="home-api-grid" id="home-api-grid">
        <div class="home-api-empty">Loading world news...</div>
      </div>
    `;

    const apiGrid = document.getElementById("home-api-grid");

    try {
      const response = await fetch("../api/api.php");
      const data = await readApiResponse(response);

      if (!response.ok) {
        throw new Error(data.message || "Could not load world news.");
      }

      const articles = Array.isArray(data.articles)
        ? data.articles.filter(isValidWorldNewsArticle)
        : [];

      apiGrid.innerHTML = "";

      if (articles.length === 0) {
        apiGrid.innerHTML =
          '<div class="home-api-empty">No world news articles available.</div>';
        return;
      }

      articles.slice(0, 4).forEach((article) => {
        apiGrid.appendChild(createWorldNewsPreviewCard(article));
      });
    } catch (error) {
      console.error("Error loading world news preview:", error);
      apiGrid.innerHTML = `<div class="home-api-empty">${error.message}</div>`;
    }
  }

  // fundi i reklamimit te 4 lajmeve te para nga bota

  // reklamimi i 4 lajmeve te para per Apple Api
  function isValidAppleNewsArticle(article) {
    return (
      article.urlToImage &&
      article.urlToImage.trim() !== "" &&
      article.title &&
      article.title.trim() !== "" &&
      article.description &&
      article.description.trim() !== "" &&
      article.url &&
      article.url.trim() !== ""
    );
  }

  function createAppleNewsPreviewCard(article) {
    const card = document.createElement("article");
    card.className = "home-api-card";

    const link = document.createElement("a");
    link.className = "home-api-link";
    link.href = article.url;
    link.target = "_blank";
    link.rel = "noopener noreferrer";

    const image = document.createElement("img");
    image.className = "home-api-img";
    image.src = article.urlToImage;
    image.alt = article.title || "Apple news image";

    const body = document.createElement("div");
    body.className = "home-api-body";

    const source = article.source?.name || "Apple news";
    body.appendChild(createTextElement("span", "apple-api-source", source));
    body.appendChild(
      createTextElement("h4", "home-api-title", article.title || "Untitled"),
    );
    body.appendChild(
      createTextElement("p", "home-api-description", article.description || ""),
    );

    if (article.publishedAt) {
      body.appendChild(
        createTextElement(
          "small",
          "apple-api-date",
          formatWorldNewsDate(article.publishedAt),
        ),
      );
    }

    link.appendChild(image);
    link.appendChild(body);
    card.appendChild(link);

    return card;
  }

  async function loadAppleNewsPreview() {
    if (!appleApi) return;

    appleApi.innerHTML = `
      <div class="home-api-heading">
        <div class="my-2">
          <p class="apple-api-eyebrow">Apple news</p>
          <h3>Latest news from Apple</h3>
        </div>
        <a class="btn btn-warning" href="../pages/feature.php">See all news</a>
      </div>
      <div class="home-api-grid" id="home-apple-api-grid">
        <div class="home-api-empty">Loading Apple news...</div>
      </div>
    `;

    const appleGrid = document.getElementById("home-apple-api-grid");

    try {
      const response = await fetch("../api/newsApi.php");
      const data = await readApiResponse(response);

      if (!response.ok) {
        throw new Error(data.message || "Could not load Apple news.");
      }

      const articles = Array.isArray(data.articles)
        ? data.articles.filter(isValidAppleNewsArticle)
        : [];

      appleGrid.innerHTML = "";

      if (articles.length === 0) {
        appleGrid.innerHTML =
          '<div class="home-api-empty">No Apple news articles available.</div>';
        return;
      }

      articles.slice(0, 4).forEach((article) => {
        appleGrid.appendChild(createAppleNewsPreviewCard(article));
      });
    } catch (error) {
      console.error("Error loading Apple news preview:", error);
      appleGrid.innerHTML = `<div class="home-api-empty">${error.message}</div>`;
    }
  }
  // fundi logjikes reklamimi i 4 lajmeve te para per APPLE api

  async function readApiResponse(response) {
    const responseText = await response.text();

    try {
      return JSON.parse(responseText);
    } catch (error) {
      return {
        message:
          responseText.trim() || "The server returned an unreadable response.",
      };
    }
  }
}); // DOMContentLoaded event listener
