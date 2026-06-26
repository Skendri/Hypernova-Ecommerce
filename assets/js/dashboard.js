document.addEventListener("DOMContentLoaded", () => {
  const state = {
    products: [],
    search: "",
  };

  const elements = {
    totalProducts: document.getElementById("totalProducts"),
    totalValue: document.getElementById("totalValue"),
    averagePrice: document.getElementById("averagePrice"),
    latestListing: document.getElementById("latestListing"),
    categoryCount: document.getElementById("categoryCount"),
    categoryBars: document.getElementById("categoryBars"),
    monthChart: document.getElementById("monthChart"),
    productRows: document.getElementById("productRows"),
    emptyDashboard: document.getElementById("emptyDashboard"),
    productSearch: document.getElementById("productSearch"),
  };

  function money(value) {
    return `$${Number(value || 0).toFixed(2)}`;
  }

  function escapeHtml(value) {
    return String(value ?? "")
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  function normalizeImagePath(imagePath) {
    if (!imagePath || imagePath.startsWith("http") || imagePath.startsWith("data:")) {
      return imagePath;
    }

    return imagePath
      .replace(/^uploads\//, "../assets/uploads/")
      .replace(/^assets\/uploads\//, "../assets/uploads/");
  }

  function getProductImage(imageValue) {
    if (!imageValue) return "https://placehold.co/160x120?text=Product";

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

  function formatDate(value) {
    if (!value) return "-";

    const date = new Date(value.replace(" ", "T"));

    if (Number.isNaN(date.getTime())) {
      return value;
    }

    return date.toLocaleDateString(undefined, {
      month: "short",
      day: "numeric",
      year: "numeric",
    });
  }

  function renderSummary(summary) {
    elements.totalProducts.textContent = summary.total_products || 0;
    elements.totalValue.textContent = money(summary.total_value);
    elements.averagePrice.textContent = money(summary.average_price);
    elements.latestListing.textContent = formatDate(summary.latest_listing);
  }

  function renderCategories(categories) {
    const entries = Object.entries(categories || {});
    const total = entries.reduce((sum, [, count]) => sum + Number(count), 0);

    elements.categoryCount.textContent = `${entries.length} categories`;
    elements.categoryBars.innerHTML = "";

    if (!entries.length) {
      elements.categoryBars.innerHTML = '<div class="text-muted">No category data yet.</div>';
      return;
    }

    entries
      .sort((a, b) => b[1] - a[1])
      .forEach(([category, count]) => {
        const percent = total ? Math.round((count / total) * 100) : 0;

        elements.categoryBars.innerHTML += `
          <div class="category-row">
            <div class="category-meta">
              <span>${escapeHtml(category)}</span>
              <span>${count} (${percent}%)</span>
            </div>
            <div class="category-track">
              <div class="category-fill" style="width:${Math.max(percent, 4)}%"></div>
            </div>
          </div>
        `;
      });
  }

  function renderMonths(monthly) {
    const entries = Object.entries(monthly || {});
    const max = Math.max(...entries.map(([, count]) => Number(count)), 1);

    elements.monthChart.innerHTML = "";

    if (!entries.length) {
      elements.monthChart.innerHTML = '<div class="text-muted">No monthly data yet.</div>';
      return;
    }

    entries.reverse().forEach(([month, count]) => {
      const height = Math.max((Number(count) / max) * 100, 8);

      elements.monthChart.innerHTML += `
        <div class="month-column">
          <div class="month-bar" style="height:${height}%"></div>
          <div class="month-value">${count}</div>
          <div class="month-label">${escapeHtml(month)}</div>
        </div>
      `;
    });
  }

  function getFilteredProducts() {
    const search = state.search.toLowerCase().trim();

    if (!search) {
      return state.products;
    }

    return state.products.filter((product) => {
      return [product.title, product.category, product.description, product.phone]
        .join(" ")
        .toLowerCase()
        .includes(search);
    });
  }

  function renderProducts() {
    const products = getFilteredProducts();
    elements.productRows.innerHTML = "";
    elements.emptyDashboard.style.display = state.products.length ? "none" : "block";

    if (!products.length) {
      if (state.products.length) {
        elements.productRows.innerHTML = `
          <tr>
            <td colspan="6" class="text-center text-muted py-4">No products match your search.</td>
          </tr>
        `;
      }
      return;
    }

    products.forEach((product) => {
      elements.productRows.innerHTML += `
        <tr>
          <td>
            <div class="product-cell">
              <img class="product-thumb" src="${escapeHtml(getProductImage(product.image))}" alt="${escapeHtml(product.title || "Product")}">
              <div>
                <strong>${escapeHtml(product.title || "Untitled")}</strong>
                <span>${escapeHtml(product.description || "")}</span>
              </div>
            </div>
          </td>
          <td>${escapeHtml(product.category || "Uncategorized")}</td>
          <td>${escapeHtml(product.status || "active")}</td>
          <td><strong>${money(product.price)}</strong></td>
          <td>${formatDate(product.created_at)}</td>
          <td class="text-end">
            <div class="action-buttons">
              <a class="btn btn-sm btn-outline-primary" href="productView.php?id=${product.id}">View</a>
              <a class="btn btn-sm btn-outline-secondary" href="editProduct.php?id=${product.id}">Edit</a>
              <button class="btn btn-sm btn-outline-danger" type="button" data-delete-id="${product.id}">Delete</button>
            </div>
          </td>
        </tr>
      `;
    });
  }

  async function deleteProduct(productId) {
    if (!window.confirm("Delete this product?")) {
      return;
    }

    const formData = new FormData();
    formData.append("id", productId);

    const response = await fetch("../api/delete_product.php", {
      method: "POST",
      body: formData,
    });
    const result = await response.json();

    if (!response.ok) {
      alert(result.message || result.error || "Could not delete product.");
      return;
    }

    await loadDashboard();
  }

  async function loadDashboard() {
    const response = await fetch("../api/dashboard_stats.php");
    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.error || "Could not load dashboard.");
    }

    state.products = Array.isArray(data.products) ? data.products : [];

    renderSummary(data.summary || {});
    renderCategories(data.categories || {});
    renderMonths(data.monthly || {});
    renderProducts();
  }

  elements.productSearch.addEventListener("input", (event) => {
    state.search = event.target.value;
    renderProducts();
  });

  elements.productRows.addEventListener("click", (event) => {
    const deleteButton = event.target.closest("[data-delete-id]");

    if (deleteButton) {
      deleteProduct(deleteButton.dataset.deleteId);
    }
  });

  loadDashboard().catch((error) => {
    console.error(error);
    elements.productRows.innerHTML = `
      <tr>
        <td colspan="6" class="text-center text-muted py-4">Could not load dashboard data.</td>
      </tr>
    `;
  });
});
