<button id="scrollTopBtn"
class="fixed bottom-6 right-6 bg-green-600 text-white p-3 rounded-full shadow-lg hidden">

<i class="fa fa-arrow-up"></i>

</button>

<script>

const btn = document.getElementById("scrollTopBtn");

window.onscroll = function(){

if(document.documentElement.scrollTop > 200){
btn.style.display = "block";
}else{
btn.style.display = "none";
}

};

btn.onclick = function(){
window.scrollTo({
top:0,
behavior:'smooth'
});
}

</script>