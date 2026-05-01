<button id="scrollTopBtn"
class="fixed bottom-6 right-6 w-10 h-10 bg-[#018b01] text-white rounded-full shadow-lg hidden flex items-center justify-center z-[100]">
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