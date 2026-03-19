$(document).ready(function () {
    // Load Province khi vừa vào trang
    $.ajax({
      url: 'controller/db_controller/getProvince.php',
      method: 'GET',
      dataType: 'json',
      success: function (data) {
        $.each(data, function (i, item) {
          $('#province').append($('<option>', {
            value: item.id,
            text: item.name
          }));
        });
      }
    });

    // Khi chọn tỉnh
    $('#province').on('change', function () {
      let province_id = $(this).val();
      $('#district').empty().append('<option value="" disabled selected hidden>District</option>');
      $('#ward').empty().append('<option value="" disabled selected hidden>Ward/Commune</option>');

      if (province_id) {
        $.ajax({
          url: 'controller/db_controller/getDistrict.php',
          method: 'GET',
          dataType: 'json',
          data: { province_id: province_id },
          success: function (data) {
            $.each(data, function (i, item) {
              $('#district').append($('<option>', {
                value: item.id,
                text: item.name
              }));
            });
          }
        });
      }
    });

    // Khi chọn quận
    $('#district').on('change', function () {
      let district_id = $(this).val();
      $('#ward').empty().append('<option value="" disabled selected hidden>Ward/Commune</option>');

      if (district_id) {
        $.ajax({
          url: 'controller/db_controller/getWard.php',
          method: 'GET',
          dataType: 'json',
          data: { district_id: district_id },
          success: function (data) {
            $.each(data, function (i, item) {
              $('#ward').append($('<option>', {
                value: item.id,
                text: item.name
              }));
            });
          }
        });
      }
    });
  });

const ID_TYPE = {
    0: "ACC",
    1: "PRD",
    2: "ORD"
}

const DELIVERY_FEE = 30000;

const order_statusTitle = {
    0: "Pending",
    1: "Processed/Delivering",
    2: "Recieved",
    3: "Cancelled",
};

const order_statusColor = {
    0: "--stat-pending",
    1: "--stat-delivering",
    2: "--stat-received",
    3: "--stat-cancel",
};

const order_statusIcon = {
    0: "fa-regular fa-hourglass-half",
    1: "fa-solid fa-truck",
    2: "fa-solid fa-circle-check",
    3: "fa-solid fa-xmark"
}

const displayEmptyHTML_cart = `
<div class="display-when-empty">
    <p>Your cart is empty... Start shopping now!</p>
</div>`;

const displayEmptyHTML_orderhistory = `
<div class="display-when-empty">
    <div class="img-container">
        <img src="./asset/img/empty-order-history.png">
    </div>
    <p>It's empty here... <a onclick="togglePage('order-history')">Start shopping
        now!</a></p>
</div>`;

const displayEmptyHTML_catalogue = `
<div class="no-result">
    <div class="no-result-h">Search returned no results!</div>
    <div class="no-result-p">Sorry, we couldn't find the product you were looking for.</div>
    <div class="no-result-i"><i class="fa-solid fa-face-sad-cry"></i></div>
</div>`;

const displayEmptyHTML_nodata = `
<div class="display-when-empty">
    <p>No data found.</p>
</div>`;

const body = document.querySelector("body");
const modalContainer = document.querySelectorAll('.modal');
const modalBox = document.querySelectorAll('.mdl-cnt');
console.log(modalBox);
const perPage = 8;

// Click vùng ngoài sẽ tắt Popup
modalContainer.forEach(item => {
    item.addEventListener('click', closeModal);
});

modalBox.forEach(item => {
    item.addEventListener('click', function (event) {
        event.stopPropagation();
    })
});

function closeModal() {
    modalContainer.forEach(item => {
        item.classList.remove('open');
    });
    // console.log(modalContainer);
    body.style.overflow = "auto";
}


async function getProduct(item) {
    try {
        let response = await fetch(`api.php?action=get_products`);
        let products = await response.json();

        if (products.error) {
            console.error(products.error);
            return null;
        }

        let product = products.find(sp => sp.id == item.id);
        if (!product) {
            console.error("Không tìm thấy sản phẩm trong database!");
            return null;
        }

        let cartItem = {
            ...product,
            ...item
        };

        return cartItem;
    } catch (error) {
        console.error("Lỗi khi lấy sản phẩm từ database:", error);
        return null;
    }
}


async function getAccounts() {
    try {
        let response = await fetch(`api.php?action=get_accounts`);
        let accounts = await response.json();

        if (accounts.error) {
            console.error(accounts.error);
            return [];
        }

        return accounts;
    } catch (error) {
        console.error("Lỗi khi lấy danh sách tài khoản:", error);
        return [];
    }
}

async function getOrders() {
    try {
        let response = await fetch(`api.php?action=get_orders`);
        let orders = await response.json();

        if (orders.error) {
            console.error(orders.error);
            return [];
        }

        return orders;
    } catch (error) {
        console.error("Lỗi khi lấy danh sách đơn hàng:", error);
        return [];
    }
}


function formatDate(date) {
    date = new Date(date); // To make sure.
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
}

function vnd(price) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(price);
}


function displayWhenEmpty(selector, innerhtml) {
    let element = document.querySelector(selector);
    if (element.innerHTML.trim() === "" || element.childElementCount === 0) {
        element.innerHTML = innerhtml;
    }
}