@extends('layout.master')

@section('body')
<style>
  .wl2-wrap{max-width:1100px;margin:40px auto;padding:0 16px;}
  .wl2-title{font-size:32px;text-align:center;margin:0;}
  .wl2-breadcrumb{font-size:12px;text-align:center;opacity:.7;margin-top:6px;}
  .wl2-card{background:#fff;border:1px solid #eee;border-radius:10px;overflow:hidden;margin-top:22px;}
  .wl2-table{width:100%;border-collapse:collapse;}
  .wl2-thead th{
    background:#f59e0b;color:#111;font-weight:600;font-size:12px;
    text-transform:uppercase;letter-spacing:.7px;
    padding:14px 12px;text-align:left;
  }
  .wl2-row td{padding:14px 12px;border-top:1px solid #eee;vertical-align:middle;}
  .wl2-remove{
    width:34px;height:34px;border-radius:6px;border:1px solid #ddd;background:#fff;
    cursor:pointer;font-size:18px;
  }
  .wl2-prod{display:flex;gap:12px;align-items:center;min-width:280px;}
  .wl2-img{
    width:54px;height:72px;border-radius:8px;object-fit:cover;border:1px solid #eee;
    cursor:pointer;
  }
  .wl2-name{font-weight:600;margin:0;cursor:pointer;}
  .wl2-price{font-weight:600;}
  .wl2-stock{font-size:13px;font-weight:600;}
  .wl2-stock.in{color:#1b8f3a;}
  .wl2-stock.out{color:#b00020;}
  .wl2-btn{
    background:#3b2417;color:#fff;border:0;border-radius:6px;
    padding:10px 14px;cursor:pointer;font-weight:600;font-size:12px;
  }
  .wl2-btn:disabled{opacity:.55;cursor:not-allowed;}
  .wl2-footer{
    display:flex;justify-content:flex-end;
    padding:14px 12px;border-top:1px solid #eee;
  }
  .wl2-actions{display:flex;gap:10px;flex-wrap:wrap;}
  .wl2-empty{text-align:center;padding:40px 20px;}
  @media (max-width: 820px){
    .wl2-thead{display:none;}
    .wl2-table, .wl2-row, .wl2-row td{display:block;width:100%;}
    .wl2-row td{border-top:0;border-bottom:1px solid #eee;}
    .wl2-row td[data-label]::before{
      content:attr(data-label);
      display:block;font-size:11px;opacity:.6;margin-bottom:6px;
    }
  }
</style>

<div class="wl2-wrap">
  <h1 class="wl2-title">Wishlist</h1>
  <div class="wl2-breadcrumb">Home / Wishlist</div>

  <div class="wl2-card" id="wlBox">
    <div class="wl2-empty">
      <h4>Your wishlist is empty</h4>
      <p>Looks like you haven't saved anything yet.</p>
      <a href="/" class="wl2-btn">Continue Shopping</a>
    </div>
  </div>
</div>

@include('footer')

<script>
document.addEventListener('DOMContentLoaded', function(){

  const wlBox = document.getElementById('wlBox');

  function money(n){
    return Number(n||0).toLocaleString('en-IN',{minimumFractionDigits:2});
  }
  function getWishlist(){
    try{ return JSON.parse(localStorage.getItem('wishlist')) || []; }
    catch{ return [] }
  }
  function setWishlist(arr){
    localStorage.setItem('wishlist', JSON.stringify(arr));
    window.dispatchEvent(new CustomEvent('wishlistUpdated'));
  }
  function getCart(){
    try{ return JSON.parse(localStorage.getItem('cart')) || []; }
    catch{ return [] }
  }
  function setCart(arr){
    localStorage.setItem('cart', JSON.stringify(arr));
    window.dispatchEvent(new CustomEvent('cartUpdated'));
  }
  function imgSrc(img){
    if(!img) return 'https://placehold.co/200x260?text=No+Image';
    img = img.replace(window.location.origin,'');
    if(img.startsWith('/')) return img;
    return '/storage/' + img;
  }

  function goProduct(id){
    if(!id) return;
    window.location.href = '/product/' + id;
  }

  function render(){
    const items = getWishlist();

    if(!items.length){
      wlBox.innerHTML = `
        <div class="wl2-empty">
          <h4>Your wishlist is empty</h4>
          <p>Looks like you haven't saved anything yet.</p>
          <a href="/" class="wl2-btn">Continue Shopping</a>
        </div>`;
      return;
    }

    let rows = '';
    items.forEach((it,i)=>{
      rows += `
      <tr class="wl2-row">
        <td data-label="Remove">
          <button class="wl2-remove" data-i="${i}" type="button">×</button>
        </td>

        <td data-label="Product">
          <div class="wl2-prod">
            <!-- ✅ IMAGE CLICK -> DETAILS PAGE -->
            <img class="wl2-img"
                 src="${imgSrc(it.image)}"
                 alt="${it.name || 'Product'}"
                 onclick="window.location.href='/product/${it.id}'"
                 onerror="this.onerror=null;this.src='https://placehold.co/200x260?text=No+Image';">

            <div>
              <p class="wl2-name" onclick="window.location.href='/product/${it.id}'">${it.name || 'Product'}</p>
            </div>
          </div>
        </td>

        <td data-label="Price" class="wl2-price">₹${money(it.price)}</td>

        <td data-label="Stock Status">
          <span class="wl2-stock in">Instock</span>
        </td>

        <td data-label="Action">
          <button class="wl2-btn wl-add" data-i="${i}" type="button">Add to Cart</button>
        </td>
      </tr>`;
    });

    wlBox.innerHTML = `
      <table class="wl2-table">
        <thead class="wl2-thead">
          <tr>
            <th></th>
            <th>Product</th>
            <th>Price</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>${rows}</tbody>
      </table>
      <div class="wl2-footer">
        <div class="wl2-actions">
          <button class="wl2-btn" id="clearWL" type="button">Clear Wishlist</button>
          <button class="wl2-btn" id="addAll" type="button">Add All to Cart</button>
        </div>
      </div>`;

    wlBox.querySelectorAll('.wl2-remove').forEach(btn=>{
      btn.onclick = ()=> {
        const i = btn.dataset.i;
        const w = getWishlist();
        w.splice(i,1);
        setWishlist(w);
        render();
      };
    });

    wlBox.querySelectorAll('.wl-add').forEach(btn=>{
      btn.onclick = ()=>{
        const i = btn.dataset.i;
        const it = getWishlist()[i];
        const cart = getCart();
        const ix = cart.findIndex(x=>x.id==it.id);
        if(ix>-1) cart[ix].qty++;
        else cart.push({...it, qty:1});
        setCart(cart);
        alert('Added to cart');
      };
    });

    document.getElementById('clearWL').onclick = ()=>{
      if(confirm('Clear wishlist?')){
        setWishlist([]);
        render();
      }
    };

    document.getElementById('addAll').onclick = ()=>{
      const cart = getCart();
      items.forEach(it=>{
        const ix = cart.findIndex(x=>x.id==it.id);
        if(ix>-1) cart[ix].qty++;
        else cart.push({...it, qty:1});
      });
      setCart(cart);
      alert('All items added to cart');
    };
  }

  render();
  window.addEventListener('wishlistUpdated', render);
});
</script>
@endsection
