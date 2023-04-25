document.addEventListener('DOMContentLoaded', function() {
    const mediaLibrary = document.querySelector('body.post-type-attachment');

    if (mediaLibrary) {
        mediaLibrary.addEventListener('click', function(e) {
            const deleteLink = e.target.closest('.delete-attachment');
            if (deleteLink) {
                e.preventDefault();
               console.log('Delete link clicked:', deleteLink);

                const currentUrl = new URL(window.location.href);
                const attachmentId = currentUrl.searchParams.get('item');
                const restUrl = `/wp-json/assignment/v1/image/${attachmentId}`;

                fetch(restUrl, {
                    headers: {'X-WP-Nonce': ajax_object.ajax_nonce} }
                )
                    .then(response => response.json())
                    .then(data => {
                        const attachedObjects = JSON.parse(data.Attached_Objects);
                        console.log('Attached objects:', attachedObjects);

                        if (attachedObjects.Posts.length > 0 || attachedObjects.Terms.length > 0 ) {
                            e.preventDefault(); // Prevent the default action only if there are attached objects

                            let message = 'Cannot delete the attachment. It is still attached to one or more objects.';

                            if (attachedObjects.Posts.length > 0) {
                                message += ' Posts: ';
                                message += attachedObjects.Posts.map(post => post.id).join(', ');
                            }
                            if (attachedObjects.Terms.length > 0) {
                                message += ' Terms: ';
                                message += attachedObjects.Terms.map(term => term.id).join(', ');
                            }

                            alert(message);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching attachment details:', error);
                        e.preventDefault(); // Prevent the default action in case of an error
                    });

            }
        }, true);
    }
});