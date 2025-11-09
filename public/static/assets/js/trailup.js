console.log('🐴')

// Sticky menu qui apparait au scroll
let topWindow = window.pageYOffset;

window.onscroll = function (){

	const currentScrollPos = window.pageYOffset;
    let topHeader = header.scrollTop;
    
    if( currentScrollPos > topWindow){
    	header.classList.add("hidden")
      header.classList.remove("initial")
      header.classList.remove("show")
    } 
    if(currentScrollPos > 400){
      header.classList.remove("hidden")
      header.classList.add("show")
    } else if(currentScrollPos == topWindow){
    	header.classList.remove("hidden")	
    	header.classList.add("initial")
    }
    // console.log('currentScrollPos :'+ currentScrollPos,'topWindow :'+ topWindow);
}

// Calcul auto de la marge du <main> pour laisser la place au header

const header = document.querySelector('header');
const headerH = header.getBoundingClientRect().height;
const body = document.querySelector('body');

body.style.marginTop = headerH+'px'

window.onresize = ()=>{
	body.style.marginTop = headerH + 'px'
}

// Dropdown menu

let boItems = document.querySelectorAll('.bo-items');
let boSubmenu = document.querySelectorAll('.bo-items_submenu');
console.log(boItems);
boItems.forEach( (item)=>{
  console.log(item);
  let arrowDropdown = document.querySelector('.arrow');

  item.addEventListener('click',()=>{
    item.classList.toggle('bo-items__open')  })
})


// Animation de la trajectoire
var path = anime.path('.trajectoire-container path');

anime({
  targets: '.trajectoire-container .suiveur',
  translateX: path('x'),
  translateY: path('y'),
  easing: 'easeInOutSine',
  duration: 8000,
  loop: true
});

anime({
  targets: '.trajectoire-container path',
  strokeDashoffset: [anime.setDashoffset, 0],
  easing: 'easeInOutSine',
  duration: 8000,
  opacity: ['100%', '0%'],  
  delay: function(el, i) { return i * 250 },
  loop: true
});




