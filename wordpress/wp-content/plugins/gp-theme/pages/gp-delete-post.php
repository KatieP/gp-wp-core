<?php

/**
 * Delete post if request comes from valid source 
 * (i.e. 'Delete this post' button clicked by user viewing their own post on fron end)
 * 
 * Display witty error message if post id not supplied. 
 * Complete with Star Wars reference for the culturally aware.
 * 
 * Author: Jesse Browne
 *         jb@greenpag.es
 * 
 **/

if ( isset($_POST['delete_this_post']) ) {
    $post_id = $_POST['delete_this_post'];
    wp_delete_post($post_id);
    echo '<p>Your post has been deleted.</p>';
} else {
    echo '<p>Move along.</p> 
          <p>Nothing to delete here.</p> 
          <p>These aren\'t the posts you\'re looking for.</p>';
}
?>