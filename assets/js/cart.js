document.addEventListener("DOMContentLoaded", () => {
  const itemsElement = document.getElementById("cartItems");
  const totalElement = document.getElementById("cartTotal");
  const quantityElement = document.getElementById("cartQuantity");
  const checkoutButton = document.getElementById("checkoutButton");
  const money = (value) => `$${Number(value || 0).toFixed(2)}`;

  function imageFor(value) {
    try { const images = JSON.parse(value); value = Array.isArray(images) ? images[0] : value; } catch (_) {}
    return value ? value.replace(/^assets\/uploads\//, "../assets/uploads/") : "https://placehold.co/160x160?text=Product";
  }

  async function request(data) {
    const response = await fetch("../api/cart.php", { method: "POST", body: new URLSearchParams(data) });
    return response.json();
  }

  async function load() {
    const response = await fetch("../api/cart.php");
    const payload = await response.json();
    const items = payload.data || [];
    itemsElement.innerHTML = "";
    if (!items.length) {
      itemsElement.innerHTML = '<div class="cart-empty">Your cart is empty. <a href="allProducts.php">Browse products</a></div>';
      totalElement.textContent = "$0.00"; quantityElement.textContent = "0"; checkoutButton.disabled = true;
      updateNavCount(0); return;
    }
    let total = 0, count = 0;
    items.forEach((item) => {
      total += Number(item.price) * item.quantity; count += Number(item.quantity);
      const card = document.createElement("article");
      card.className = "card cart-item shadow-sm border-0 mb-3";
      card.innerHTML = `<div class="card-body d-flex gap-3"><img src="${imageFor(item.image)}" alt=""><div class="flex-grow-1"><span class="badge text-bg-light">${item.category || "Product"}</span><h2 class="h5 mt-2">${item.title}</h2><p class="text-muted small mb-2">${item.size ? `Size: ${item.size}` : ""}${item.size && item.color ? " · " : ""}${item.color ? `Color: ${item.color}` : ""}</p><strong>${money(item.price)}</strong></div><div class="text-end"><button class="btn btn-sm btn-outline-danger remove">Remove</button><div class="input-group input-group-sm mt-3 quantity"><button class="btn btn-outline-secondary decrease">−</button><input class="form-control text-center" value="${item.quantity}" readonly><button class="btn btn-outline-secondary increase">+</button></div></div></div>`;
      card.querySelector(".remove").onclick = async () => { await request({ action: "remove", key: item.key }); load(); };
      card.querySelector(".decrease").onclick = async () => { await request({ action: "update", key: item.key, quantity: item.quantity - 1 }); load(); };
      card.querySelector(".increase").onclick = async () => { await request({ action: "update", key: item.key, quantity: item.quantity + 1 }); load(); };
      itemsElement.appendChild(card);
    });
    totalElement.textContent = money(total); quantityElement.textContent = count; checkoutButton.disabled = false; updateNavCount(count);
  }

  function updateNavCount(count) { const badge = document.getElementById("cartCount"); if (badge) badge.textContent = count; }
  checkoutButton.addEventListener("click", () => alert("Checkout is the next step. Your cart is ready for a payment integration."));
  load();
});
