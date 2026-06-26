const form = document.getElementById("editProductForm");
const imageInput = document.getElementById("imageInput");
const previewContainer = document.getElementById("previewContainer");
const descriptionTextarea = document.getElementById("editor");
const viewProductLink = document.getElementById("viewProductLink");
let descriptionEditor = null;

const productId = new URLSearchParams(window.location.search).get("id");

if (window.ClassicEditor && descriptionTextarea) {
  ClassicEditor.create(descriptionTextarea)
    .then((editor) => {
      descriptionEditor = editor;
      loadProduct();
    })
    .catch((error) => {
      console.error(error);
      loadProduct();
    });
} else {
  loadProduct();
}

function normalizeImagePath(imagePath) {
  if (!imagePath || imagePath.startsWith("http") || imagePath.startsWith("data:")) {
    return imagePath;
  }

  return imagePath
    .replace(/^uploads\//, "../assets/uploads/")
    .replace(/^assets\/uploads\//, "../assets/uploads/");
}

function getProductImages(imageValue) {
  if (!imageValue) return [];

  try {
    const parsedImages = JSON.parse(imageValue);

    if (Array.isArray(parsedImages)) {
      return parsedImages.map(normalizeImagePath);
    }
  } catch (error) {
    return [normalizeImagePath(imageValue)];
  }

  return [];
}

function renderImagePreview(images) {
  previewContainer.innerHTML = "";

  images.forEach((image) => {
    const preview = document.createElement("img");
    preview.src = image;
    preview.alt = "Product image";
    previewContainer.appendChild(preview);
  });
}

function getDescriptionHtml() {
  if (descriptionEditor) {
    return descriptionEditor.getData();
  }

  return descriptionTextarea ? descriptionTextarea.value : "";
}

function getDescriptionText(html) {
  const container = document.createElement("div");
  container.innerHTML = html;
  return container.textContent.trim();
}

async function loadProduct() {
  if (!productId) {
    alert("Missing product id.");
    window.location.href = "dashboard.php";
    return;
  }

  const response = await fetch(`../api/fetch_products.php?id=${encodeURIComponent(productId)}`);
  const products = await response.json();
  const product = Array.isArray(products) ? products[0] : null;

  if (!response.ok || !product || Number(product.is_owner) !== 1) {
    alert("Product was not found or you do not have permission to edit it.");
    window.location.href = "dashboard.php";
    return;
  }

  document.getElementById("productId").value = product.id;
  document.getElementById("productTitle").value = product.title || "";
  document.getElementById("productCategory").value = product.category || "";
  document.getElementById("productPrice").value = product.price || "";
  document.getElementById("productStatus").value = product.status || "active";
  document.getElementById("productPhone").value = product.phone || "";

  if (descriptionEditor) {
    descriptionEditor.setData(product.description || "");
  } else {
    descriptionTextarea.value = product.description || "";
  }

  viewProductLink.href = `productView.php?id=${encodeURIComponent(product.id)}`;
  renderImagePreview(getProductImages(product.image));
}

imageInput.addEventListener("change", function () {
  const files = Array.from(this.files);

  if (files.length > 5) {
    alert("You can upload a maximum of 5 images.");
    this.value = "";
    return;
  }

  previewContainer.innerHTML = "";

  files.forEach((file) => {
    const reader = new FileReader();

    reader.onload = function (event) {
      const preview = document.createElement("img");
      preview.src = event.target.result;
      preview.alt = file.name;
      previewContainer.appendChild(preview);
    };

    reader.readAsDataURL(file);
  });
});

form.addEventListener("submit", async function (event) {
  event.preventDefault();

  const descriptionHtml = getDescriptionHtml();

  if (!getDescriptionText(descriptionHtml)) {
    alert("Please enter a product description.");
    return;
  }

  if (descriptionEditor) {
    descriptionEditor.updateSourceElement();
  }

  const formData = new FormData(form);
  formData.set("description", descriptionHtml);

  const response = await fetch("../api/update_product.php", {
    method: "POST",
    body: formData,
  });
  const result = await response.json().catch(() => ({
    message: "The server returned an unreadable response.",
  }));

  alert(result.message || "Product request finished.");

  if (response.ok) {
    window.location.href = "dashboard.php";
  }
});

