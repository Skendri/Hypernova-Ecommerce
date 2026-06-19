document.addEventListener("DOMContentLoaded", () => {
  const state = {
    images: [],
    activeImage: 0,
    quantity: 1,
    product: null,
  };

  const elements = {
    page: document.querySelector(".product-page"),
    mainImage: document.getElementById("mainProductImage"),
    thumbnailStrip: document.getElementById("thumbnailStrip"),
    category: document.getElementById("productCategory"),
    title: document.getElementById("productTitle"),
    price: document.getElementById("productPrice"),
    oldPrice: document.getElementById("oldPrice"),
    description: document.getElementById("productDescription"),
    longDescription: document.getElementById("productLongDescription"),
    previous: document.querySelector(".gallery-control.previous"),
    next: document.querySelector(".gallery-control.next"),
    pincodeForm: document.getElementById("pincodeForm"),
    deliveryMessage: document.getElementById("deliveryMessage"),
    quantity: document.getElementById("quantityValue"),
    decreaseQuantity: document.getElementById("decreaseQuantity"),
    increaseQuantity: document.getElementById("increaseQuantity"),
    addToCart: document.getElementById("addToCart"),
    buyNow: document.getElementById("buyNow"),
  };

  function normalizeImagePath(imagePath) {
    if (!imagePath || imagePath.startsWith("http") || imagePath.startsWith("data:")) {
      return imagePath;
    }

    return imagePath
      .replace(/^uploads\//, "../assets/uploads/")
      .replace(/^assets\/uploads\//, "../assets/uploads/");
  }

  function getProductImages(imageValue) {
    if (!imageValue) return ["https://placehold.co/900x900?text=Product"];

    try {
      const parsedImages = JSON.parse(imageValue);

      if (Array.isArray(parsedImages) && parsedImages.length > 0) {
        return parsedImages.map(normalizeImagePath);
      }
    } catch (error) {
      return [normalizeImagePath(imageValue)];
    }

    return [normalizeImagePath(imageValue)];
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

  function setRichDescription(element, html, fallback) {
    const cleanHtml = sanitizeProductDescription(html);
    element.innerHTML = cleanHtml || fallback;
  }

  function setControlsDisabled(isDisabled) {
    [
      elements.previous,
      elements.next,
      elements.decreaseQuantity,
      elements.increaseQuantity,
      elements.addToCart,
      elements.buyNow,
    ].forEach((button) => {
      if (button) button.disabled = isDisabled;
    });

    if (elements.pincodeForm) {
      elements.pincodeForm
        .querySelectorAll("input, button")
        .forEach((field) => {
          field.disabled = isDisabled;
        });
    }
  }

  function renderSkeleton() {
    elements.page.classList.add("is-skeleton");
    setControlsDisabled(true);

    state.product = null;
    state.images = [];
    state.activeImage = 0;

    elements.category.textContent = "Loading";
    elements.title.textContent = "Product title loading";
    elements.price.textContent = "$000.00";
    elements.oldPrice.textContent = "$000.00";
    elements.description.textContent =
      "Product description loading while the item details are found.";
    elements.longDescription.textContent =
      "More product information will appear here when a matching item exists.";
    elements.deliveryMessage.textContent = "Checking product availability.";
    elements.mainImage.removeAttribute("src");
    elements.mainImage.alt = "";

    elements.thumbnailStrip.innerHTML = "";

    for (let index = 0; index < 5; index++) {
      const thumbnail = document.createElement("div");
      thumbnail.className = "thumbnail-skeleton";
      elements.thumbnailStrip.appendChild(thumbnail);
    }
  }

  function money(value) {
    const numericPrice = Number(value);

    if (Number.isNaN(numericPrice)) {
      return "$0.00";
    }

    return `$${numericPrice.toFixed(2)}`;
  }

  function setActiveImage(index) {
    if (!state.images.length) return;

    state.activeImage = (index + state.images.length) % state.images.length;
    elements.mainImage.classList.add("is-changing");

    window.setTimeout(() => {
      elements.mainImage.src = state.images[state.activeImage];
      elements.mainImage.classList.remove("is-changing");
    }, 160);

    document.querySelectorAll(".thumbnail-button").forEach((button, buttonIndex) => {
      button.classList.toggle("active", buttonIndex === state.activeImage);
    });
  }

  function renderThumbnails() {
    elements.thumbnailStrip.innerHTML = "";

    state.images.slice(0, 5).forEach((image, index) => {
      const button = document.createElement("button");
      button.type = "button";
      button.className = `thumbnail-button${index === state.activeImage ? " active" : ""}`;
      button.setAttribute("aria-label", `View image ${index + 1}`);

      const thumbnail = document.createElement("img");
      thumbnail.src = image;
      thumbnail.alt = `${state.product.title || "Product"} thumbnail ${index + 1}`;

      button.appendChild(thumbnail);
      button.addEventListener("click", () => setActiveImage(index));
      elements.thumbnailStrip.appendChild(button);
    });
  }

  function renderProduct(product) {
    elements.page.classList.remove("is-skeleton");
    setControlsDisabled(false);

    const price = Number(product.price) || 0;
    const oldPrice = price * 1.38;

    state.product = product;
    state.images = getProductImages(product.image);
    state.activeImage = 0;

    elements.category.textContent = product.category || "Product";
    elements.title.textContent = product.title || "Untitled Product";
    elements.price.textContent = money(price);
    elements.oldPrice.textContent = money(oldPrice);
    setRichDescription(elements.description, product.description, "");
    setRichDescription(
      elements.longDescription,
      product.description,
      "This seller has not added a longer description yet.",
    );

    elements.mainImage.src = state.images[0];
    elements.mainImage.alt = product.title || "Product image";
    renderThumbnails();
  }

  function showToast(message) {
    const existingToast = document.querySelector(".toast-note");

    if (existingToast) {
      existingToast.remove();
    }

    const toast = document.createElement("div");
    toast.className = "toast-note";
    toast.textContent = message;
    document.body.appendChild(toast);

    requestAnimationFrame(() => toast.classList.add("show"));

    window.setTimeout(() => {
      toast.classList.remove("show");
      window.setTimeout(() => toast.remove(), 240);
    }, 2400);
  }

  async function loadProduct() {
    renderSkeleton();

    const productId = new URLSearchParams(window.location.search).get("id");

    if (!productId) {
      return;
    }

    try {
      const response = await fetch("../api/fetch_products.php");
      const products = await response.json();
      const product = products.find((item) => String(item.id) === String(productId));

      if (product) {
        renderProduct(product);
      }
    } catch (error) {
      console.error("Could not load product:", error);
    }
  }

  function updateQuantity(nextQuantity) {
    state.quantity = Math.max(1, Math.min(9, nextQuantity));
    elements.quantity.textContent = state.quantity;
  }

  elements.previous.addEventListener("click", () => {
    setActiveImage(state.activeImage - 1);
  });

  elements.next.addEventListener("click", () => {
    setActiveImage(state.activeImage + 1);
  });

  document.querySelectorAll(".size-options button").forEach((button) => {
    button.addEventListener("click", () => {
      document
        .querySelectorAll(".size-options button")
        .forEach((option) => option.classList.remove("active"));
      button.classList.add("active");
    });
  });

  document.querySelectorAll(".color-choice").forEach((button) => {
    button.addEventListener("click", () => {
      document
        .querySelectorAll(".color-choice")
        .forEach((option) => option.classList.remove("active"));
      button.classList.add("active");
    });
  });

  elements.pincodeForm.addEventListener("submit", (event) => {
    event.preventDefault();

    const input = elements.pincodeForm.querySelector("input");
    const pincode = input.value.trim();

    elements.deliveryMessage.textContent =
      pincode.length >= 4
        ? `Delivery available to ${pincode}. Estimated arrival in 3-5 days.`
        : "Please enter a valid pincode.";
  });

  elements.decreaseQuantity.addEventListener("click", () => {
    updateQuantity(state.quantity - 1);
  });

  elements.increaseQuantity.addEventListener("click", () => {
    updateQuantity(state.quantity + 1);
  });

  elements.addToCart.addEventListener("click", () => {
    showToast(`${state.quantity} item added to cart.`);
  });

  elements.buyNow.addEventListener("click", () => {
    showToast("Ready for checkout.");
  });

  loadProduct();
});
