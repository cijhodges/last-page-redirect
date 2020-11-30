<?php
namespace LastPageRedirect;

class AdminPage
{
    public $redirects;

    public function __construct()
    {
        $this->redirects = array();

        switch ( $_SERVER['REQUEST_METHOD'] ) {
            case 'GET':
                global $wpdb;
                $sql = "SELECT * FROM `{$wpdb->prefix}last_page_redirect`";

                if ( isset( $_GET['orderby' ] ) && in_array( $_GET['orderby'], array( 'referal_url', 'date' ) ) ) {
                    $sql .= " ORDER BY `" . filter_var( $_GET['orderby'], FILTER_SANITIZE_STRING ) . "`";

                    $order = 'DESC';

                    if ( isset( $_GET['order'] ) && in_array( strtoupper( $_GET['order'] ), array( 'ASC', 'DESC' ) ) ) {
                        $order = strtoupper( filter_var( $_GET['order'], FILTER_SANITIZE_STRING ) );
                    }

                    $sql .= " " . $order;
                }

                $results = $wpdb->get_results( $sql );

                for ($i = 0; $i < count( $results ); $i++ ) {
                    $this->redirects[] = new Redirect( $results[$i] );
                }
                break;

            case 'POST':
                if ( isset( $_POST['id'] ) ) {
                    if ( !isset( $_POST['method'] ) || !in_array( $_POST['method'], array( 'DELETE', 'PUT' ) ) ) return false;
                    $method = $_POST['method'];

                    $Redirect = new Redirect( array(
                        'id'    => intval( $_POST['id'] )
                    ) );

                    if ( !$Redirect->read() ) {
?>
                    <h1>Redirect Not Found</h1>
                    <p>The redirect you are trying to update was not found.</p>
                    <p><a href="">Return to "Last Page Redirect" settings</a></p>
<?php
                        die();
                    }

                    switch ( $method ) {
                        case 'DELETE';
                            if ( !$Redirect->delete() ) {
?>
                    <h1>Redirect Not Deleted</h1>
                    <p>The redirect you are trying to delete could not be deleted. Please try again.</p>
                    <p><a href="">Return to "Last Page Redirect" settings</a></p>
<?php
                                die();
                            }

?>
                    <script type="text/javascript">
                        window.location.href = '/wp-admin/admin.php?page=last-page-redirect&deleted=<?= $Redirect->id; ?>';
                    </script>
<?php
                            break;
                    
                        case 'PUT':
                            if ( !isset( $_POST['referal_url'] ) || !isset( $_POST['operator'] ) ) {
?>
                    <h1>Redirect Not Updated</h1>
                    <p>The request is missing a requried field(s).</p>
                    <p><a href="">Return to "Last Page Redirect" settings</a></p>
<?php
                                die();
                            }

                            $Redirect->set( array(
                                'referal_url'   => $_POST['referal_url'],
                                'operator'      => $_POST['operator']
                            ) );

                            if ( !$Redirect->update() ) {
?>
                    <h1>Redirect Not Updated</h1>
                    <p>The updates that were requested could not be made. Please try again.</p>
                    <p><a href="">Return to "Last Page Redirect" settings</a></p>
<?php
                                die();
                            }
?>
                    <script type="text/javascript">
                         window.location.href = '/wp-admin/admin.php?page=last-page-redirect&updated=<?= $Redirect->id; ?>';
                    </script>
<?php
                            break;
                    }
                    die();
                }

                if ( 
                    !isset( $_POST['referal_url'] ) 
                    || !isset( $_POST['operator'] )
                    || !in_array( $_POST['operator'], array( 'contains', 'exact match' ) )
                ) {
?>
                <h1>Redirect Not Created</h1>
                <p>You are missing required fields. Please use the admin form to submit your request.</p>
                <p><a href="">Return to "Last Page Redirect" settings</a></p>
<?php
                    die();
                }

                $Redirect = new Redirect( array(
                    'referal_url'   => $_POST['referal_url'],
                    'operator'      => $_POST['operator']
                ) );

                if ( !$Redirect->create() ) {
?>
                <h1>Redirect Not Created</h1>
                <p>The redirect was not created. This is likely due to a error in the formatting of the fields. Please check your fields again and retry.</p>
<?php
                    die();
                }

?>
                <script type="text/javascript">
                    window.location.href = '/wp-admin/admin.php?page=last-page-redirect&added=<?= $Redirect->id; ?>';
                </script>
<?php
                die();
                break;
        }
    }

    public function render()
    {
        $orderby = $_GET['orderby'];
        $order = $_GET['order'];

        if ( !$orderby ) $orderby = 'referal_url';
        if ( !$order ) $order = 'desc';

        $columns = array(
            array(
                'name'      => 'referal_url',
                'label'     => 'Referal Domain',
                'primary'   => true
            ),
            array(
                'name'      => 'date',
                'label'     => 'Date',
                'primary'   =>  false
            )
        );
?>
        <link href="<?= LAST_PAGE_REDIRECT_URL; ?>/admin/css/main.css" type="text/css" rel="stylesheet">
        <div class="wrap">
            <h1 class="wp-heading-inline" style="margin-bottom: 1rem;">Last Page Redirect</h1>
            <a href="#" class="page-title-action js-last-page-redirect__new">Add New</a>
            <table class="wp-list-table widefat fixed striped table-view-list pages">
                <thead>
                    <tr>
<?php
        foreach ( $columns as $column ) {
?>
                        <th scope="col" id="title" class="manage-column column-<?= $column['name']; ?> <?php if ( $column['primary'] ) { echo ' column-primary'; } ?> <?php if ( $orderby === $column['name'] ) { echo 'sorted '; if ( $order === 'desc' ) { echo 'desc'; } else { echo 'asc'; } } else { echo 'sortable'; } ?>">
                            <a href="/wp-admin/admin.php?page=last-page-redirect&orderby=<?= $column['name']; ?>&order=<?php if ( $orderby === $column['name'] ) { if ( $order === 'desc' ) { echo 'asc'; } else { echo 'desc'; } } else { echo 'desc'; } ?>">
                                <span><?= $column['label']; ?></span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
<?php
        }
?>
                    </tr>
                </thead>
                <tbody id="the-list">
<?php
        if ( count( $this->redirects ) === 0 ) {
?>
            <tr>
                <td>
                    Looks like there are no redirects. <a href="#" class="js-last-page-redirect__new">Add one</a>.
                </td>
                <td></td>
            </tr>
<?php
        } else {
            foreach ( $this->redirects as $redirect ) {
?>
                <tr class="post-<?= $redirect->id; ?> post" data-id="<?= $redirect->id; ?>">
                    <td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
                        <div class="locked-info">
                            <span class="locked-avatar"></span>
                            <span class="locked-text"></span>
                        </div>
                        <strong>
                            <a class="row-title js-last-page-redirect__edit" href="#" aria-label="“<?= $redirect->referal_url; ?>” (Edit)"><?= $redirect->referal_url; ?></a>
                        </strong>
                        <div class="hidden" id="inline_<?= $redirect->id; ?>">
	                        <div class="referal_url"><?= $redirect->referal_url; ?></div>
	                        <div class="operator"><?= $redirect->operator; ?></div>
	                        <div class="date"><?= $redirect->date; ?></div>
                        </div>
                        <div class="row-actions">
                            <span class="edit">
                                <a href="#" class="js-last-page-redirect__edit" aria-label="Edit “<?= $redirect->referal_url; ?>”">Edit</a> | 
                            </span>
                            <span class="trash">
                                <a href="#" class="js-last-page-redirect__delete" aria-label="Move “<?= $redirect->referal_url; ?>” to the Trash">Trash</a>
                            </span>
                        </div>
                    </td>
                    <td class="date column-date" data-colname="Date">Created on<br><?= $redirect->date; ?></td>
                </tr>
<?php
            }
        }
?>
                </tbody>
            </table>
        </div>
        <script type="text/template" id="js-last-page-redirect-item__template">
            <% 
                var isCurrent = false; 
                if (typeof referal_url !== 'undefined') isCurrent = true;
            %>
            <h2><% if (isCurrent) { %>Edit<% } else { %>New<% } %> Redirect: <% if(isCurrent) { %><%= referal_url %><% } %></h2>
            <form class="post-edit js-last-page-redirect-<% if (isCurrent) { %>edit<% } else { %>add<% } %>-form"<% if (isCurrent) { %> data-id="<%= id %>"<% } %> method="<% if (isCurrent) { %>POST<% } else { %>POST<% } %>">
                <% if (isCurrent) { %><input type="hidden" name="method" value="PUT"><input type="hidden" name="id" value="<%= id %>"><% } %>
                <div class="post-edit__field is-required">
                    <label for="referal_url"><strong>Referal Domain:</strong></label>
                    <input type="text" name="referal_url" id="referal_url" <% if (isCurrent) { %> value="<%= referal_url %>"<% } %>required>
                    <span><em>This will be the referal domain that will trigger the redirect.</em></span>
                </div>
                <div class="post-edit__field is-required">
                    <label for="operator"><strong>Method of Matching:</strong></label>
                    <select name="operator" id="operator" required>
                        <option value="contains"<% if (isCurrent && operator === 'contains') { %> selected<% } %>>Contains</option>
                        <option value="exact match"<% if (isCurrent && operator === 'exact match') { %> selected<% } %>>Exact Match
                    </select>
                    <span><em>This will determine how the referal domain is matched.</em></span>
                </div>
                <input type="submit" value="Submit" class="button button-primary js-last-page-redirect__submit">
            </form>
        </script>
        <script type="text/template" id="js-last-page-redirect-delete__template">
            <h2>Are you sure you want to delete this redirect?</h2>
            <p>
                ID: <%= id %><br />
                Referal Domain: <%= referal_url %><br />
                Method of Matching: <%= operator %>
            </p>
            <form method="POST">
                <input type="hidden" name="id" value="<%= id %>">
                <input type="hidden" name="method" value="DELETE">
                <input type="submit" class="button button-primary" value="Yes, delete this redirect">
                <br /><br />
                <a href="#" class="js-modal__close">Cancel</a>
            </form>
        </script>
        <script type="text/template" id="js-modal__template">
            <div class="modal">
                <div class="modal__background js-modal__close"></div>
                <div class="modal__holder">
                    <button class="modal__close js-modal__close">X</button>
                    <div class="modal__content">
                        <%= content %>
                    </div>
                </div>
            </div>
        </script>
        <script type="text/javascript" src="<?= LAST_PAGE_REDIRECT_URL; ?>admin/js/main.js" defer></script>
<?php
    }
}
