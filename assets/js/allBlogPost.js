document.addEventListener("DOMContentLoaded", function () {
  const postsGrid = document.getElementById("all-blog-posts");
  const fallbackImage = "https://placehold.co/900x520?text=Blog+Post";

  function createTextElement(tagName, className, text) {
    const element = document.createElement(tagName);
    element.className = className;
    element.textContent = text;
    return element;
  }

  function sanitizeBlogContent(html) {
    const template = document.createElement("template");
    const allowedTags = new Set([
      "A",
      "B",
      "BLOCKQUOTE",
      "BR",
      "CODE",
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
    element.innerHTML = sanitizeBlogContent(html);
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

  function createBlogCard(post) {
    const link = document.createElement("a");
    link.className = "all-blog-link";
    link.href = `fullPost-page.php?id=${encodeURIComponent(post.id)}`;
    link.setAttribute("aria-label", `Read ${post.title || "blog post"}`);

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

    meta.appendChild(
      createTextElement(
        "span",
        "all-blog-views",
        `${Number(post.view_count || 0)} views`,
      ),
    );

    body.appendChild(meta);
    body.appendChild(
      createTextElement("h2", "all-blog-title", post.title || "Untitled post"),
    );
    body.appendChild(
      createTextElement("p", "all-blog-excerpt", post.excerpt || ""),
    );

    if (post.content) {
      body.appendChild(
        createRichTextElement("div", "all-blog-content", post.content),
      );
    }

    article.appendChild(image);
    article.appendChild(body);

    link.appendChild(article);

    return link;
  }

  function createPostSection(title, posts, emptyMessage) {
    const section = document.createElement("section");
    section.className = "all-blog-section";

    section.appendChild(createTextElement("h2", "all-blog-section-title", title));

    const grid = document.createElement("div");
    grid.className = "all-blog-grid";

    if (posts.length === 0) {
      grid.innerHTML = `<div class="all-blog-empty">${emptyMessage}</div>`;
    } else {
      posts.forEach((post) => {
        grid.appendChild(createBlogCard(post));
      });
    }

    section.appendChild(grid);

    return section;
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

      const latestPosts = posts.slice(0, 3);
      const latestPostIds = new Set(latestPosts.map((post) => String(post.id)));
      const mostViewedPosts = [...posts]
        .filter((post) => !latestPostIds.has(String(post.id)))
        .sort((firstPost, secondPost) => {
          const viewDifference =
            Number(secondPost.view_count || 0) - Number(firstPost.view_count || 0);

          if (viewDifference !== 0) {
            return viewDifference;
          }

          return (
            new Date((secondPost.created_at || "").replace(" ", "T")).getTime() -
            new Date((firstPost.created_at || "").replace(" ", "T")).getTime()
          );
        })
        .slice(0, 3);

      postsGrid.appendChild(
        createPostSection("Latest posts", latestPosts, "No latest posts yet."),
      );
      postsGrid.appendChild(
        createPostSection(
          "Most viewed posts",
          mostViewedPosts,
          "No more posts have been viewed yet.",
        ),
      );
    } catch (error) {
      console.error("Error loading all blog posts:", error);
      postsGrid.innerHTML = `<div class="all-blog-empty">${error.message}</div>`;
    }
  }

  loadAllBlogPosts();
});
