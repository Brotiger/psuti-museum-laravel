function startLoading(){
    document.body.classList.remove('loaded');
    document.body.classList.remove('loaded_hiding');
}

function stopLoading(){
    setTimeout(function(){
        document.body.classList.add('loaded_hiding');
        document.body.classList.add('loaded');
    }, 500);
}