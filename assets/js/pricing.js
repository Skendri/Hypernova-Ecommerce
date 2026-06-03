const blogForm = document.getElementById("blogForm");
const postsGrid = document.getElementById("postsGrid");
const coverInput = document.getElementById("coverImage");
const coverPreview = document.getElementById("coverPreview");
const formMessage = document.getElementById("formMessage");
const publishButton = document.getElementById("publishButton");
const statusInput = document.getElementById("status");
const refreshPosts = document.getElementById("refreshPosts");
const titleInput = document.getElementById("title");
const excerptInput = document.getElementById("excerpt");
const titleCount = document.getElementById("titleCount");
const excerptCount = document.getElementById("excerptCount");
const postCount = document.getElementById("postCount");

const fallbackImage = "https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=900&q=80";

function setMessage(message, type) {
  formMessage.textContent = message;
  formMessage.className = `form-message is-${type}`;
}

function clearMessage() {
  formMessage.textContent = "";
  formMessage.className = "form-message";
}

function updateCounters() {
  titleCount.textContent = titleInput.value.length;
  excerptCount.textContent = excerptInput.value.length;
}

function normalizeImagePath(imagePath) {
  if (!imagePath) return fallbackImage;
  if (imagePath.startsWith("http") || imagePath.startsWith("data:")) return imagePath;

  return imagePath
    .replace(/^uploads\//, "../assets/uploads/")
    .replace(/^assets\/uploads\//, "../assets/uploads/");
}

function formatDate(dateValue) {
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

function createPostCard(post) {
  const article = document.createElement("article");
  article.className = "post-card";

  const image = document.createElement("img");
  image.src = normalizeImagePath(post.cover_image);
  image.alt = post.title || "Blog post cover";

  const body = document.createElement("div");
  body.className = "post-card-body";

  const meta = document.createElement("div");
  meta.className = "post-meta";

  const status = document.createElement("span");
  status.className = `status-badge ${post.status}`;
  status.textContent = post.status;

  const date = document.createElement("span");
  date.className = "post-date";
  date.textContent = formatDate(post.created_at);

  const title = document.createElement("h3");
  title.textContent = post.title;

  const excerpt = document.createElement("p");
  excerpt.textContent = post.excerpt;

  const author = document.createElement("div");
  author.className = "post-author";
  author.textContent = `By ${post.author_name}`;

  meta.append(status, date);
  body.append(meta, title, excerpt, author);
  article.append(image, body);

  return article;
}

async function loadPosts() {
  postsGrid.innerHTML = '<div class="empty-state">Loading posts...</div>';

  try {
    const response = await fetch("../api/fetch_blog_posts.php?scope=mine", {
      credentials: "same-origin",
    });
    const data = await readApiResponse(response);

    if (!response.ok) {
      throw new Error(data.message || "Could not load blog posts.");
    }

    postCount.textContent = data.length;
    postsGrid.innerHTML = "";

    if (data.length === 0) {
      postsGrid.innerHTML = `
        <div class="empty-state">
          <h3>No posts yet</h3>
          <p>Write your first blog post and it will appear here.</p>
        </div>
      `;
      return;
    }

    data.forEach((post) => {
      postsGrid.appendChild(createPostCard(post));
    });
  } catch (error) {
    postsGrid.innerHTML = `<div class="empty-state">${error.message}</div>`;
  }
}

coverInput.addEventListener("change", function () {
  clearMessage();
  coverPreview.innerHTML = "";
  coverPreview.hidden = true;

  const file = this.files[0];

  if (!file) return;

  if (file.size > 3 * 1024 * 1024) {
    setMessage("Cover image must be 3MB or smaller.", "error");
    this.value = "";
    return;
  }

  const allowedTypes = ["image/jpeg", "image/png", "image/gif", "image/webp"];

  if (!allowedTypes.includes(file.type)) {
    setMessage("Only JPG, PNG, GIF, and WEBP cover images are allowed.", "error");
    this.value = "";
    return;
  }

  const reader = new FileReader();

  reader.onload = function (event) {
    const image = document.createElement("img");
    image.src = event.target.result;
    image.alt = file.name;
    coverPreview.appendChild(image);
    coverPreview.hidden = false;
  };

  reader.readAsDataURL(file);
});

statusInput.addEventListener("change", function () {
  publishButton.textContent = this.value === "draft" ? "Save Draft" : "Publish Post";
});

titleInput.addEventListener("input", updateCounters);
excerptInput.addEventListener("input", updateCounters);
refreshPosts.addEventListener("click", loadPosts);

blogForm.addEventListener("submit", async function (event) {
  event.preventDefault();
  clearMessage();

  const content = document.getElementById("content").value.trim();

  if (content.length < 50) {
    setMessage("Post content must be at least 50 characters.", "error");
    return;
  }

  publishButton.disabled = true;
  publishButton.textContent = statusInput.value === "draft" ? "Saving..." : "Publishing...";

  try {
    const response = await fetch("../api/save_blog_post.php", {
      method: "POST",
      credentials: "same-origin",
      body: new FormData(blogForm),
    });

    const data = await readApiResponse(response);

    if (!response.ok) {
      throw new Error(data.message || "Could not save this blog post.");
    }

    setMessage(data.message, "success");
    blogForm.reset();
    coverPreview.innerHTML = "";
    coverPreview.hidden = true;
    updateCounters();
    await loadPosts();
  } catch (error) {
    setMessage(error.message, "error");
  } finally {
    publishButton.disabled = false;
    publishButton.textContent = statusInput.value === "draft" ? "Save Draft" : "Publish Post";
  }
});

updateCounters();
loadPosts();

async function readApiResponse(response) {
  const responseText = await response.text();

  try {
    return JSON.parse(responseText);
  } catch (error) {
    return {
      message: responseText.trim() || "The server returned an unreadable response.",
    };
  }
}
