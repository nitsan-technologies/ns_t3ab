var nestedSortables = [].slice.call(document.querySelectorAll('.t3ab-sortable'));
let dialog = document.querySelector('.sortable-wrap .sortable-input');

// Loop through each nested sortable element
for (var i = 0; i < nestedSortables.length; i++) {
    new Sortable(nestedSortables[i], {
        group: 'nested',
        animation: 150,
        fallbackOnBody: true,
        swapThreshold: 0.65
    });
}

function addButtonDeleteEvent (button)
{
    button.addEventListener('click', function(e){
        e.preventDefault();
        this.closest(".list-group-item.typo3-form").remove();
    });
}

// delete buttons
var deleteButtonList = Array.from(document.querySelectorAll('.sortable-wrap button.delete'));
if (Array.isArray(deleteButtonList)){
    deleteButtonList.forEach(function(item){
        addButtonDeleteEvent(item);
    });
}
