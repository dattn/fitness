require([
    'dojo/dom',
    'dojo/on',
    'dojo/_base/event',
    'dojo/request',
    'dojo/dom-construct',
    'dojo/dom-class',
    'dojo/dom-geometry',
    'dojo/dom-style',
    'dojo/_base/config',
    'dojo/domReady!'
], function(dom, on, event, request, domConstruct, domClass, domGeometry,
            domStyle, config) {

    // init variables
    var searchInputNode    = dom.byId('searchInput');
    var rowsContainerNode  = dom.byId('rowsContainer');
    var loadingOverlayNode = dom.byId('loadingOverlay');
    var userListNode       = dom.byId('userList');
    var updateRowsRequest;
    var searchTimeout;

    var showLoading = function() {
        var position = domGeometry.position(userListNode);
        domStyle.set(loadingOverlayNode, {
            left: position.x + 'px',
            top: position.y + 'px',
            width: position.w + 'px',
            height: position.h + 'px'
        });
        domClass.remove(loadingOverlayNode, 'hidden');
    }

    var hideLoading = function() {
        domClass.add(loadingOverlayNode, 'hidden');
    }

    var updateRows = function() {
        updateRowsRequest = request.post(config.app.baseUri+'usermanager/listingRows', {
            data: {
                search: searchInputNode.value
            }
        });
        console.log(updateRowsRequest);
        updateRowsRequest.then(function(data) {
            if (data) {
                domConstruct.place(data, rowsContainerNode, 'only');
            } else {
                domConstruct.empty(rowsContainerNode);
            }
            hideLoading();
        });
    };

    var onKeyUp = function() {
        showLoading();
        if (searchTimeout)
            clearTimeout(searchTimeout);
        if (updateRowsRequest)
            updateRowsRequest.cancel();
        searchTimeout = setTimeout(updateRows, 400);
    }

    // update rows on search
    on(searchInputNode, 'keyup', onKeyUp);

    // update rows on load
    updateRows();

});
