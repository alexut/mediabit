document.addEventListener('DOMContentLoaded', function() {
    const adminScreen = document.body.getAttribute('class');
    const postTypeMatch = adminScreen.match(/post-type-(\w+)/);
    const postType = postTypeMatch ? postTypeMatch[1] : null;

    const types = {
        'page': {
            colsToSpan: 4,
            groups: {
                'Servicii Complete': {
                    ids: [478, 489, 491], // Example IDs, adjust as needed
                    visible: true // Change visibility here
                },
                'Solutii B2B': {
                    ids: [85, 487,493,495,497], // Example IDs, adjust as needed
                    visible: true // Change visibility here
                },
                'Teste': {
                    ids: [74, 49, 57], // Example IDs, adjust as needed
                    visible: true // Change visibility here
                }
            }
        }
    };

    if (types[postType]) {
        const { colsToSpan, groups } = types[postType];
        const tbody = document.querySelector('#the-list');
        
        if (!tbody) {
            console.log('Failed to find the table body.');
            return;
        }

        // Append groups and pages at the end after processing all
        Object.entries(groups).forEach(([groupName, group]) => {
            const groupRow = document.createElement('tr');
            groupRow.innerHTML = `<td colspan="${colsToSpan}"><strong>${groupName}</strong></td>`;
            groupRow.classList.add('group-header', group.visible ? 'expanded' : 'collapsed');

            tbody.appendChild(groupRow); // Append the group header to the tbody

            // Process each page in the group
            group.ids.forEach(id => {
                const pageRow = document.querySelector(`#post-${id}`);
                if (pageRow) {
                    tbody.appendChild(pageRow); // Move the page under the group header
                    pageRow.style.display = group.visible ? '' : 'none'; // Set visibility based on the group
                }
            });

            groupRow.addEventListener('click', function() {
                const isVisible = this.classList.toggle('expanded');
                this.classList.toggle('collapsed');
                group.ids.forEach(id => {
                    const pageRow = document.querySelector(`#post-${id}`);
                    if (pageRow) {
                        pageRow.style.display = isVisible ? '' : 'none';
                    }
                });
            });
        });
    }
});
