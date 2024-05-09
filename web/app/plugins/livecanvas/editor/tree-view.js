// TREE VIEW: CORE FUNCTIONS /////////////////

function createTreeHTML(node) {
    // Base case: if node is not an element node, return an empty string
    if (node.nodeType !== Node.ELEMENT_NODE) {
        return '';
    }

    // Create the current node's HTML representation
    const tag = node.tagName.toLowerCase();
    const id = node.id ? `#${node.id}` : '';
    const classList = Array.from(node.classList);
    const classes = classList.slice(0, 2).map(cls => `.${cls}`).join(' ');
    const fullClasses = classList.map(cls => `.${cls}`).join(' ');

    // Determine the item type and get the corresponding icon HTML
    const itemType = getLayoutElementType(CSSelector(node)); // Assumes this function returns a string identifier for the item
    const iconHtml = itemType ? getCustomIcon('panel-title-' + itemType) : '';

    // Generate the HTML for children recursively
    const childrenHTML = Array.from(node.childNodes).map(createTreeHTML).join('');

    // Combine the current node with its children
    return `
    <li class="tree-view-item" data-selector="${CSSelector(node)}" title="${fullClasses}">
        <div class="tree-view-item-content-wrapper">
            <span class="tree-item-icon">${iconHtml}</span>
            <span class="tree-item-tagname">${tag}</span>
            ${id ? `<span class="tree-item-id">${id}</span>` : ''}
            <span class="tree-item-classes" >${classes || ''}</span>
        </div>
        ${childrenHTML ? `<ul class="tree-children" hidden>${childrenHTML}</ul>` : ''}
    </li>`;
}

function renderTreeHTMLStructure(selector) {
    const rootNode = doc.querySelector(selector);
    if (!rootNode) return ''; // If the root node is not found, return an empty string
    const treeHTML = createTreeHTML(rootNode);
    return `<ul class="tree-view-container">${treeHTML}</ul>`;
}

function redrawTreePart(selector) {
  const specificNode = doc.querySelector(selector);
  if (!specificNode) {
    console.error("Specific node not found:", selector);
    return;
  }

  // a map to store the visibility state
  const visibilityStates = new Map();
  const treeItems = document.querySelectorAll("#tree-body .tree-view-item");
  treeItems.forEach((item) => {
    const itemSelector = item.getAttribute("data-selector");
    const isHidden = item.querySelector(".tree-children")?.hidden;
    visibilityStates.set(itemSelector, isHidden);
  });

  // Generate the new HTML for the subtree
  const newSubtreeHTML = createTreeHTML(specificNode);

  // Replace the old subtree with the new one in the DOM
  const existingItem = document.querySelector(
    `#tree-body [data-selector="${CSSelector(specificNode)}"]`,
  );
  if (existingItem) {
    const parentContainer = existingItem.parentNode;
    existingItem.outerHTML = newSubtreeHTML;

    // search in the map to know if that node was hidden or not
    const newTreeItems = parentContainer.querySelectorAll(".tree-view-item");
    newTreeItems.forEach((newItem) => {
      const newItemSelector = newItem.getAttribute("data-selector");
      const wasHidden = visibilityStates.get(newItemSelector);
      const childrenContainer = newItem.querySelector(".tree-children");
      if (childrenContainer && wasHidden !== undefined) {
        childrenContainer.hidden = wasHidden;
      }
    });
  } else {
    console.error(
      "Existing tree item not found for selector:",
      CSSelector(specificNode),
    );
  }
}



//TREE VIEW: HANDLE USER ACTIONS ////////////

//USER CLICKS TREEVIEW ICON IN MAIN MENU BAR 
class TreeViewWindow {
  constructor() {
    if (!TreeViewWindow.instance) {
        this.winBox = null;
        TreeViewWindow.instance = this;
    }
    return TreeViewWindow.instance;
  }

  open() {
    if (this.winBox) {
      return; // Window is already open
    }
    this.winBox = new WinBox({
        id: "tree-window",
        title: "Tree View",
        class: ["no-full", "no-max", "my-theme"],
        html: $("#tree-view-window-content-template").html(),
        background: "#e83e8c",
        border: 4,
        width: 350,
        height: "96%",
        minheight: 55,
        minwidth: 100,
        x: "right",
        top: 45,
        right: 0,
        onclose: () => {
            $("#toggle-tree-view").removeClass("is-active");
            this.winBox = null; 
        }
    });
    document.getElementById('tree-body').innerHTML = renderTreeHTMLStructure('main#lc-main');
    $('#tree-body').find(".tree-view-item-content-wrapper").first().click(); 
  }

  close() {
    if (this.winBox) {
      this.winBox.close();
      this.winBox = null;
    }
  }
}



//USER CLICKS TREEVIEW ICON IN MAIN MENU BAR 
$("body").on("click", "#toggle-tree-view", function (e) {
    e.preventDefault(); 

    $(this).toggleClass("is-active");
    const treeViewWindow = new TreeViewWindow();

    if ($(this).hasClass("is-active")) {
        treeViewWindow.open();
    } else {
        treeViewWindow.close();
    }
});



//USER HOVERS TREE VIEW ITEM: HIGHLIGHT SAME PART IN PREVIEW
$("body").on("mouseenter", ".tree-view-item-content-wrapper", function (e) {
    
    if ($("#tree-context-menu").is("visible")) return false;

    selector = $(this).parent().attr("data-selector"); 
    previewFrame.contents().find(".lc-highlight-currently-editing").removeClass("lc-highlight-currently-editing"); //for security
    previewFrame.contents().find(selector).addClass("lc-highlight-currently-editing");

    //$("#tree-body").find(".active").removeClass("active");
    //$(this).addClass("active");
});

//USER CLICKS TREE HEAD LINK: EXPAND ALL
$("body").on("click", "#tree-expand-all", function (e) {
    e.preventDefault();
    e.stopPropagation();
    $(".tree-view-item").each(function () { 
        const item = $(this).find(".tree-children")[0];
        if (item) {
            item.hidden = false;
        }
    });
});

//USER CLICKS TREE HEAD LINK: COLLAPSE ALL
$("body").on("click", "#tree-collapse-all", function (e) {
    e.preventDefault();
    e.stopPropagation();
    $(".tree-view-item").each(function () { 
        const item = $(this).find(".tree-children")[0];
        if (item) {
            item.hidden = true;
        }
    });
    // $('#tree-body').find(".tree-view-item").first().click(); //open root

    const treeContainer = document.querySelector('#tree-body .tree-view-container');
    if (treeContainer) {
      const firstTreeItem = treeContainer.querySelector('.tree-view-item');
      if (firstTreeItem) {
        const treeChildren = firstTreeItem.querySelector('.tree-children');
        if (treeChildren) {
          treeChildren.removeAttribute('hidden');
        }
      }
    }

});

//USER CLICKS TREE VIEW ITEM: OPEN COLLAPSIBLE AND SCROLL PREVIEW TO ELEMENT
$("body").on("click", ".tree-view-item-content-wrapper", function (e) {
  e.preventDefault();
  e.stopPropagation();

  // Ensure we use the data-selector from the parent .tree-view-item
  const selector = $(this).closest('.tree-view-item').attr("data-selector");
 
  // Toggle visibility of .tree-children directly within the clicked .tree-view-item
  const item = $(this).closest('.tree-view-item').find('> .tree-children').first();
  //  replace item.toggle(); causing problem adding display: none
  if (item[0].hasAttribute('hidden')) {
      // Remove the 'hidden' attribute if it is set
      item[0].removeAttribute('hidden');
  } else {
      // Add the 'hidden' attribute if it is not set
      item[0].setAttribute('hidden', '');
  }

  // If the tree item is not hidden, scroll the preview frame to the element
  if (!item.is(":hidden")) {
      previewFrame.contents().find("html, body").animate({
          scrollTop: previewFrame.contents().find(selector).offset().top
      }, 100, 'linear');
  }
});

//USER RIGHT-CLICKS A TREE VIEW ITEM: open the context menu
$("body").on("contextmenu", ".tree-view-item", function (e) {
    e.preventDefault();
    e.stopPropagation(); 
    let contextMenu = document.getElementById("tree-context-menu");
    contextMenu.setAttribute("data-selector", $(this).attr("data-selector"));

    //x and y position of mouse or touch
    let mouseX = e.clientX || e.touches[0].clientX;
    let mouseY = e.clientY || e.touches[0].clientY;
    //height and width of menu
    let menuHeight = contextMenu.getBoundingClientRect().height;
    let menuWidth = contextMenu.getBoundingClientRect().width;
    //width and height of screen
    let width = window.innerWidth;
    let height = window.innerHeight;
    //If user clicks/touches near right corner
    if (width - mouseX <= 200) {
        contextMenu.style.borderRadius = "5px 0 5px 5px";
        contextMenu.style.left = width - menuWidth + "px";
        contextMenu.style.top = mouseY + "px";
        //right bottom
        if (height - mouseY <= 200) {
            contextMenu.style.top = mouseY - menuHeight + "px";
            contextMenu.style.borderRadius = "5px 5px 0 5px";
        }
    }
    //left
    else {
        contextMenu.style.borderRadius = "0 5px 5px 5px";
        contextMenu.style.left = mouseX + "px";
        contextMenu.style.top = mouseY + "px";
        //left bottom
        if (height - mouseY <= 200) {
            contextMenu.style.top = mouseY - menuHeight + "px";
            contextMenu.style.borderRadius = "5px 5px 5px 0";
        }
    }
    //display the menu
    contextMenu.style.visibility = "visible";
});

//if user clicks outside the menu HIDE CONTEXT MENU (for click devices)
$("body").on("click", "*", function (e) {
    let contextMenu = document.getElementById("tree-context-menu");
    if (!contextMenu.contains(e.target)) {
        contextMenu.style.visibility = "hidden";
    }
});



//CONTEXTUAL MENU ACTIONS HOVER HANDLER
$("body").on("mouseover", "[data-tree-item-action]", function (e) {
    const selector = $(this).parent().attr("data-selector");  
    //highlight item in tree
    $("#tree-body .tree-view-item.active").removeClass("active");
    $("#tree-body .tree-view-item[data-selector='" + selector + "']").addClass("active");
});


//CONTEXTUAL MENU ACTIONS CLICK HANDLER
$("body").on("click", "[data-tree-item-action]", function (e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById("tree-context-menu").style.visibility = "hidden";
    const selector = $(this).parent().attr("data-selector"); 

    switch ($(this).attr("data-tree-item-action")) {
        case "edit-properties":
            openSidePanel(selector);
            break;
        case "edit-html":
            openHtmlEditor(selector);
            break;
        case "copy-content":
            copyToClipboard(selector);
            break;
        case "paste-content":
            pasteFromClipboard(selector)
            break;
        case "duplicate-item":
            duplicateElement(selector);
            break;
        case "delete-item":
            deleteElement(selector);
            break;
        case "move-up":
            moveElementUp(selector);
            break;
        case "move-down":
            moveElementDown(selector);
            break;
        default:
            console.log("Something went horribly wrong...");
    }
});

