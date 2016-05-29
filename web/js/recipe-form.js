var $collectionIngredientsHolder;
var $collectionDirectionsHolder;

var $addIngredientLink = $('<a href="#" class="add_ingredient_link btn btn-success">Add an ingredient</a>');
var $newIngredientLinkLi = $('<li></li>').append($addIngredientLink);

var $addDirectionLink = $('<a href="#" class="add_direction_link btn btn-success">Add a direction</a>');
var $newDirectionLinkLi = $('<li></li>').append($addDirectionLink);

jQuery(document).ready(function() {
    
    // ======================== INGREDIENTS COLLECTION =====================================

    $collectionIngredientsHolder = $('ul.ingredients');
    $collectionIngredientsHolder.append($newIngredientLinkLi);

    $collectionIngredientsHolder.data('index', $collectionIngredientsHolder.find(':input').length);

    $collectionIngredientsHolder.find('li.ingredientLi').each(function() {
        addIngredientFormDeleteLink($(this));
    });

    $addIngredientLink.on('click', function(e) {
        e.preventDefault();
        addIngredientForm($collectionIngredientsHolder, $newIngredientLinkLi);
    });

    // ======================== DIRECTIONS COLLECTION =====================================

    $collectionDirectionsHolder = $('ul.directions');
    $collectionDirectionsHolder.append($newDirectionLinkLi);

    $collectionDirectionsHolder.data('index', $collectionDirectionsHolder.find(':input').length);

    $collectionDirectionsHolder.find('li.directionLi').each(function() {
        addDirectionFormDeleteLink($(this));
    });

    $addDirectionLink.on('click', function(e) {
        e.preventDefault();
        addDirectionForm($collectionDirectionsHolder, $newDirectionLinkLi);
    });

    $('ul.directions, ul.ingredients').on('click','button',function(e){
        e.preventDefault();
        $(this).parent().remove();
    });


});

function addIngredientForm($collectionHolder, $newIngredientLinkLi) {
    
    var prototype = $collectionHolder.data('prototype');

    var index = $collectionHolder.data('index');

    var newForm = prototype.replace(/__name__/g, index);
    
    $collectionHolder.data('index', index + 1);

    var $newFormLi = $('<li></li>').append(newForm);
    $newIngredientLinkLi.before($newFormLi);

    addIngredientFormDeleteLink($newFormLi);
}

function addIngredientFormDeleteLink($ingredientFormLi) {
    var $removeFormA = $('<a href="#"class="btn btn-default delete-form-button">Delete</a>');
    $ingredientFormLi.append($removeFormA);

    $removeFormA.on('click', function(e) {
        e.preventDefault();
        $ingredientFormLi.remove();
    });
}

function addDirectionForm($collectionHolder, $newDirectionLinkLi) {
    
    var prototype = $collectionHolder.data('prototype');

    var index = $collectionHolder.data('index');

    var newForm = prototype.replace(/__name__/g, index);
    
    $collectionHolder.data('index', index + 1);

    var $newFormLi = $('<li></li>').append(newForm);
    $newDirectionLinkLi.before($newFormLi);

    addDirectionFormDeleteLink($newFormLi);
}

function addDirectionFormDeleteLink($directionFormLi) {
    var $removeFormA = $('<a href="#"class="btn btn-default delete-form-button">Delete</a>');
    $directionFormLi.append($removeFormA);

    $removeFormA.on('click', function(e) {
        e.preventDefault();
        $directionFormLi.remove();
    });
}