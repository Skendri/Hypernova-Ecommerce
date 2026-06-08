document.addEventListener("DOMContentLoaded", function () {
  const postsGrid = document.getElementById("all-blog-posts");
  const fallbackImage = "https://placehold.co/900x520?text=Blog+Post";

  function createTextElement(tagName, className, text) {
    const element = document.createElement(tagName);
    element.className = className;
    element.textContent = text;
    return element;
  }

  function normalizeImagePath(imagePath) {
    if (!imagePath) return fallbackImage;

    if (imagePath.startsWith("http") || imagePath.startsWith("data:")) {
      return imagePath;
    }

    return imagePath
      .replace(/^uploads\//, "../assets/uploads/")
      .replace(/^assets\/uploads\//, "../assets/uploads/");
  }

  function formatDate(dateValue) {
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

  function shortenText(text, maxLength) {
    if (!text) return "";
    if (text.length <= maxLength) return text;

    return `${text.slice(0, maxLength).trim()}...`;
  }

  function createBlogCard(post) {
    const article = document.createElement("article");
    article.className = "all-blog-card";

    const image = document.createElement("img");
    image.className = "all-blog-img";
    image.src = normalizeImagePath(post.cover_image);
    image.alt = post.title || "Blog post cover";

    const body = document.createElement("div");
    body.className = "all-blog-body";

    const meta = document.createElement("div");
    meta.className = "all-blog-meta";
    meta.appendChild(
      createTextElement(
        "span",
        "all-blog-author",
        post.author_name || "Unknown author",
      ),
    );

    if (post.created_at) {
      meta.appendChild(
        createTextElement("span", "all-blog-date", formatDate(post.created_at)),
      );
    }

    body.appendChild(meta);
    body.appendChild(
      createTextElement("h2", "all-blog-title", post.title || "Untitled post"),
    );
    body.appendChild(
      createTextElement("p", "all-blog-excerpt", post.excerpt || ""),
    );

    if (post.content) {
      body.appendChild(
        createTextElement(
          "p",
          "all-blog-content",
          shortenText(post.content, 220),
        ),
      );
    }

    article.appendChild(image);
    article.appendChild(body);

    return article;
  }

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

  async function loadAllBlogPosts() {
    if (!postsGrid) return;

    try {
      const response = await fetch(
        "../api/fetch_blog_posts.php?scope=published",
        {
          credentials: "same-origin",
        },
      );
      const posts = await readApiResponse(response);

      if (!response.ok) {
        throw new Error(posts.message || "Could not load blog posts.");
      }

      postsGrid.innerHTML = "";

      if (!Array.isArray(posts) || posts.length === 0) {
        postsGrid.innerHTML =
          '<div class="all-blog-empty">No published blog posts yet.</div>';
        return;
      }

      posts.forEach((post) => {
        postsGrid.appendChild(createBlogCard(post));
      });
    } catch (error) {
      console.error("Error loading all blog posts:", error);
      postsGrid.innerHTML = `<div class="all-blog-empty">${error.message}</div>`;
    }
  }

  loadAllBlogPosts();
});
