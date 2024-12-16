function handleRefresh(flag = true)
{
    if(flag){
        $(window).on('beforeunload', preventRefresh);
    } else {
        $(window).off('beforeunload', preventRefresh);
    }
}

function preventRefresh(event)
{
    event.preventDefault();
    event.returnValue = true;
}