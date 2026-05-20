document.addEventListener("DOMContentLoaded", () => {
  const fallbackProduct = {
    id: "preview",
    title: "Premium Wireless Headphones",
    category: "Electronics",
    price: "180.00",
    description:
      "Clean sound, soft cushions, and all-day battery life made for work, travel, and everyday listening.",
    image: JSON.stringify([
      "https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=1000&q=80",
      "https://images.unsplash.com/photo-1484704849700-f032a568e944?auto=format&fit=crop&w=1000&q=80",
      "https://images.unsplash.com/photo-1546435770-a3e426bf472b?auto=format&fit=crop&w=1000&q=80",
      "https://images.unsplash.com/photo-1524678606370-a47ad25cb82a?auto=format&fit=crop&w=1000&q=80",
    ]),
  };

  const state = {
    images: [],
    activeImage: 0,
    quantity: 1,
    product: fallbackProduct,
  };

  const elements = {
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

  function getProductImages(imageValue) {
    if (!imageValue) return JSON.parse(fallbackProduct.image);

    try {
      const parsedImages = JSON.parse(imageValue);

      if (Array.isArray(parsedImages) && parsedImages.length > 0) {
        return parsedImages;
      }
    } catch (error) {
      return [imageValue];
    }

    return [imageValue];
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
    const price = Number(product.price) || 180;
    const oldPrice = price * 1.38;

    state.product = product;
    state.images = getProductImages(product.image);
    state.activeImage = 0;

    elements.category.textContent = product.category || "Product";
    elements.title.textContent = product.title || "Untitled Product";
    elements.price.textContent = money(price);
    elements.oldPrice.textContent = money(oldPrice);
    elements.description.textContent =
      product.description || fallbackProduct.description;
    elements.longDescription.textContent =
      product.description ||
      "Built for daily use with premium materials, smooth controls, and a checkout-ready shopping flow.";

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
    const productId = new URLSearchParams(window.location.search).get("id");

    if (!productId) {
      renderProduct(fallbackProduct);
      return;
    }

    try {
      const response = await fetch("./api/fetch_products.php");
      const products = await response.json();
      const product = products.find((item) => String(item.id) === String(productId));

      renderProduct(product || fallbackProduct);

      if (!product) {
        showToast("Product not found, showing preview item.");
      }
    } catch (error) {
      console.error("Could not load product:", error);
      renderProduct(fallbackProduct);
      showToast("Could not load this product right now.");
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
