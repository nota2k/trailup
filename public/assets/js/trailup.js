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
// console.log(boItems);
boItems.forEach( (item)=>{
  // console.log(item);
  let arrowDropdown = item.querySelector('.arrow');
  
  // Ne pas ajouter l'événement si l'élément n'a pas de sous-menu
  if (!item.querySelector('.bo-items_submenu')) {
    return;
  }

  item.addEventListener('click',(e)=>{
    // Ne pas fermer si on clique sur un lien du sous-menu
    if (e.target.tagName === 'A' && e.target.closest('.bo-items_submenu')) {
      return;
    }
    
    item.classList.toggle('bo-items__open');
    
    // Changer la flèche
    if (arrowDropdown) {
      if (item.classList.contains('bo-items__open')) {
        // Changer vers flèche vers le bas
        arrowDropdown.innerHTML = '<path d="M0.81592 0.394136C0.364745 0.891819 0.364745 1.63834 0.81592 2.13603L6.34283 8.23264C7.24518 9.22801 8.59869 9.22801 9.50104 8.23264L15.0279 2.13603C15.4791 1.63834 15.4791 0.891819 15.0279 0.394136C14.5767 -0.103547 13.9 -0.103547 13.4488 0.394136L8.71147 5.6198C8.2603 6.11748 7.58354 6.11748 7.13237 5.6198L2.39506 0.394136C1.94388 -0.103547 1.26709 -0.103547 0.81592 0.394136Z" />';
        arrowDropdown.classList.remove('up');
        arrowDropdown.classList.add('down');
      } else {
        // Changer vers flèche vers le haut
        arrowDropdown.innerHTML = '<path d="M14.8746 8.6059C15.3258 8.10822 15.3258 7.36171 14.8746 6.86402L9.34772 0.767398C8.44537 -0.227967 7.09186 -0.227967 6.18951 0.767398L0.6626 6.86402C0.211425 7.36171 0.211425 8.10822 0.6626 8.6059C1.11377 9.10359 1.79056 9.10359 2.24174 8.6059L6.97905 3.38024C7.43022 2.88256 8.10698 2.88256 8.55815 3.38024L13.2955 8.6059C13.7467 9.10359 14.4234 9.10359 14.8746 8.6059Z" />';
        arrowDropdown.classList.remove('down');
        arrowDropdown.classList.add('up');
      }
    }
  })
})

// Sorting annonces
let annonces = document.querySelectorAll('.post-wrapper')
let allAnnonces = [...annonces]


let sortDistance = document.querySelector('.sort-distance')


window.addEventListener('load', ()=>{
  annonces.forEach( item => {
    let dist = item.querySelector('.distance')
    let distValue = item.querySelector('.distance').textContent;
    dist.setAttribute("distance", distValue)
  })
});


// function getValueSort(){
//   let result = "<?php $result = 'distance'; ?>"
// }


// sortDistance.addEventListener('click',()=>{

//     let i = 0;
//     do {
//       i += 1;
//     } while (i < allAnnonces.length);

// });

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





