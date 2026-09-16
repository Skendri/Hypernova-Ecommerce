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
    const productUrl = `productView.php?id=${encodeURIComponent(product.id)}`;

    const card = document.createElement("a");
    card.className = "product-card uploaded-product-card";
    card.href = productUrl;

    // const divLink = document.createElement("a");
    // divLink.className = "h-100 uploaded-product-link";
    // divLink.href = productUrl;
    // card.appendChild(divLink);

    // const imageLink = document.createElement("a");
    // imageLink.className = "uploaded-product-media-link";
    // imageLink.href = productUrl;
    // card.href = productUrl;
    // imageLink.setAttribute("aria-label", `View ${product.title || "product"}`);

    const image = document.createElement("img");
    image.className = "uploaded-product-img product-image-img";
    image.src = getProductImage(product.image);
    image.alt = product.title || "Product image";
    // imageLink.appendChild(image);

    // container parent for description at the card
    const body = document.createElement("div");
    body.className = "product-card-content uploaded-product-body";

    const meta = document.createElement("div");
    meta.className = "meta-row product-card-meta";

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
        "rating",
        `★ ${product.rating || "4.9"}`,
      ),
    );

    body.appendChild(meta);
    body.appendChild(
      createTextElement(
        "h3",
        "product-card-title uploaded-product-title",
        product.title || "Untitled",
      ),
    );
    // this logic here is description from CKeditor and is for card at home.php page
    // body.appendChild(
    //   createRichTextElement(
    //     "div",
    //     "uploaded-product-description",
    //     product.description || "",
    //   ),
    // );

    body.appendChild(
      createTextElement(
        "p",
        "product-card-description",
        product.description || `Seller: ${product.owner_name || "Unknown user"}`,
      ),
    );

    const priceRow = document.createElement("div");
    priceRow.className = "price-row product-card-price-row";
    priceRow.appendChild(
      createTextElement("span", "price", `$${product.price || "0.00"}`),
    );
    priceRow.appendChild(
      createTextElement("span", "add-btn", "+"),
    );
    body.appendChild(priceRow);

    const imageFrame = document.createElement("div");
    imageFrame.className = "product-image uploaded-product-media";
    imageFrame.appendChild(image);
    card.append(imageFrame, body);

    return card;
  }

  async function loadUploadedProducts() {
    if (!userProducts) return;

    try {
      const response = await fetch("../api/fetch_products.php?limit=8");
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

      products.slice(0, 8).forEach((product) => {
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

  function createBlogPostCard(post, variant = "bottom") {
    const link = document.createElement("a");
    link.className = `home-blog-link story-${variant}`;
    link.href = `fullPost-page.php?id=${encodeURIComponent(post.id)}`;
    link.setAttribute("aria-label", `Read ${post.title || "blog post"}`);

    const card = document.createElement("article");
    card.className = `home-blog-card bento-card bento-${variant}`;

    const image = document.createElement("img");
    image.className = "home-blog-img";
    image.src = getBlogImage(post.cover_image);
    image.alt = post.title || "Blog post cover";

    const body = document.createElement("div");
    body.className = "home-blog-body bento-card-body";

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
    body.appendChild(createTextElement("h4", "home-blog-title", post.title || "Untitled post"));
    body.appendChild(createTextElement("p", "home-blog-excerpt", post.excerpt || ""));

    if (variant === "hero") {
      const overlay = document.createElement("div");
      overlay.className = "bento-hero-overlay";
      overlay.appendChild(createTextElement("span", "bento-label", "Featured article"));
      overlay.appendChild(createTextElement("h3", "bento-hero-title", post.title || "Untitled post"));
      image.insertAdjacentElement("afterend", overlay);
      card.classList.add("bento-image-card");
      card.innerHTML = "";
      card.appendChild(image);
      card.appendChild(overlay);

      const footer = document.createElement("div");
      footer.className = "bento-hero-footer";
      footer.appendChild(createTextElement("strong", "", post.author_name || "Unknown author"));
      footer.appendChild(createTextElement("span", "", `${formatBlogDate(post.created_at) || "Recently published"} · Read essay →`));
      card.appendChild(footer);
      link.innerHTML = "";
      link.appendChild(card);
      return link;
    }

    if (variant === "side") {
      card.innerHTML = "";
      card.classList.add("bento-side-card");
      card.appendChild(image);
      card.appendChild(body);
      link.innerHTML = "";
      link.appendChild(card);
      return link;
    }

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
        "../api/fetch_blog_posts.php?scope=published&limit=7",
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

      const [hero, ...remainingPosts] = posts.slice(0, 7);
      if (hero) {
        blogGrid.appendChild(createBlogPostCard(hero, "hero"));
      }

      const sidePosts = remainingPosts.slice(0, 3);
      if (sidePosts.length > 0) {
        const sideColumn = document.createElement("div");
        sideColumn.className = "bento-side-column";
        sidePosts.forEach((post) => sideColumn.appendChild(createBlogPostCard(post, "side")));
        blogGrid.appendChild(sideColumn);
      }

      const bottomPosts = remainingPosts.slice(3, 6);
      if (bottomPosts.length > 0) {
        const bottomRow = document.createElement("div");
        bottomRow.className = "bento-bottom-row";
        bottomPosts.forEach((post) => bottomRow.appendChild(createBlogPostCard(post)));
        blogGrid.appendChild(bottomRow);
      }
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

  const worldFallbackArticles = [
    ["The Verge", "It's Greg Brockman's OpenAI now", "OpenAI enters a new chapter as the technology industry watches closely.", "https://images.unsplash.com/photo-1677442136019-21780ecad995?auto=format&fit=crop&w=900&q=80"],
    ["Android Central", "Global smartphone sales hit a major slump", "The mobile market is shifting toward fewer, more considered upgrades.", "https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=900&q=80"],
    ["MacRumors", "Ceramic Apple Watch rumored to return", "A familiar material may be making a refined return to the wrist.", "https://images.unsplash.com/photo-1546868871-7041f2a55e12?auto=format&fit=crop&w=900&q=80"],
    ["TechCrunch", "The quiet rise of smaller creative tools", "Independent makers are reshaping the hardware landscape one focused product at a time.", "https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=900&q=80"],
  ].map(([source, title, description, image]) => ({ source: { name: source }, title, description, urlToImage: image, publishedAt: "2026-08-20" }));

  const appleFallbackArticles = [
    ["MacRumors", "Apple Watch Series 11 discounted by $100", "The latest wearable is seeing meaningful savings across several configurations.", "https://images.unsplash.com/photo-1551816230-ef5deaed4a2a?auto=format&fit=crop&w=900&q=80"],
    ["9to5Mac", "A closer look at the next generation of Apple design", "Small refinements continue to define the most useful products in the lineup.", "https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=900&q=80"],
    ["AppleInsider", "The studio desk gets a thoughtful refresh", "Better tools disappear into the workflow, leaving more room for the work itself.", "https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=900&q=80"],
    ["Cult of Mac", "What makes a great creative setup", "A balanced workspace is built around rhythm, focus, and a few excellent essentials.", "https://images.unsplash.com/photo-1497215728101-856f4ea42174?auto=format&fit=crop&w=900&q=80"],
  ].map(([source, title, description, image]) => ({ source: { name: source }, title, description, urlToImage: image, publishedAt: "2026-08-20" }));

  function createMarqueeCard(article) {
    const card = document.createElement("article");
    card.className = "marquee-card";

    const image = document.createElement("img");
    image.src = article.urlToImage;
    image.alt = article.title || "News image";

    const body = document.createElement("div");
    body.className = "marquee-card-body";
    body.appendChild(createTextElement("span", "", article.source?.name || "News desk"));
    body.appendChild(createTextElement("h3", "", article.title || "Untitled story"));
    body.appendChild(createTextElement("p", "", article.description || ""));
    body.appendChild(createTextElement("time", "", formatWorldNewsDate(article.publishedAt) || "Today"));

    card.append(image, body);
    return card;
  }

  function renderInfiniteCarousel(container, kicker, title, articles, link, linkLabel) {
    container.innerHTML = "";

    const heading = document.createElement("div");
    heading.className = "carousel-heading";
    const headingCopy = document.createElement("div");
    headingCopy.appendChild(createTextElement("p", "", kicker));
    headingCopy.appendChild(createTextElement("h2", "", title));
    const headingLink = document.createElement("a");
    headingLink.className = "text-link";
    headingLink.href = link;
    headingLink.textContent = `${linkLabel} →`;
    heading.append(headingCopy, headingLink);

    const frame = document.createElement("div");
    frame.className = "marquee-frame";
    const track = document.createElement("div");
    track.className = "marquee-track";
    const cards = articles.slice(0, 8);
    [...cards, ...cards].forEach((article, index) => {
      const card = createMarqueeCard(article);
      if (index >= cards.length) card.setAttribute("aria-hidden", "true");
      track.appendChild(card);
    });
    frame.appendChild(track);
    container.append(heading, frame);
  }

  async function loadWorldNewsPreview() {
    if (!apiAnotherPage) return;

    try {
      const response = await fetch("../api/api.php");
      const data = await readApiResponse(response);

      const articles = Array.isArray(data.articles)
        ? data.articles.filter(isValidWorldNewsArticle)
        : [];
      renderInfiniteCarousel(apiAnotherPage, "Latest pulse & tech radar", "Curated technology dispatches & market briefs", articles.length ? articles : worldFallbackArticles, "worldNews.php", "Explore all feeds");
    } catch (error) {
      console.error("Error loading world news preview:", error);
      renderInfiniteCarousel(apiAnotherPage, "Latest pulse & tech radar", "Curated technology dispatches & market briefs", worldFallbackArticles, "worldNews.php", "Explore all feeds");
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

    try {
      const response = await fetch("../api/newsApi.php");
      const data = await readApiResponse(response);

      const articles = Array.isArray(data.articles)
        ? data.articles.filter(isValidAppleNewsArticle)
        : [];
      renderInfiniteCarousel(appleApi, "Apple intelligence", "The latest from the Apple orbit", articles.length ? articles : appleFallbackArticles, "feature.php", "See all news");
    } catch (error) {
      console.error("Error loading Apple news preview:", error);
      renderInfiniteCarousel(appleApi, "Apple intelligence", "The latest from the Apple orbit", appleFallbackArticles, "feature.php", "See all news");
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
