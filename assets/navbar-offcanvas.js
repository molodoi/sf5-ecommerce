if(document.querySelector('[data-bs-toggle="offcanvas"]')){
    document.querySelector('[data-bs-toggle="offcanvas"]').addEventListener('click', function () {
        document.querySelector('.offcanvas-collapse').classList.toggle('open')
    })
}