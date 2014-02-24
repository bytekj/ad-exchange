<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->

<div class="page">
    <h3>Your content</h3>

    <table class="content_table">
        <tr>
            <th>Content Type</th>
            <th>Link/Content</th>
            <th>Upload Date</th>
            <th>status</th>
        </tr>
        <?php
        $objContent = new Content();

        $content = $objContent->getCurrentUserContent();
        echo "<pre>";
        print_r($images);
        echo "</pre>";
        foreach ($content as $media) {
            if ($media['url']) {
                $s = urldecode($media['url']);
            } else {
                $s = $media['content'];
            }
            echo "<tr>" .
            "<td>" . $media['content_type'] . "</td>" .
            "<td>" . $s . "</td>" .
            "<td>" . $media['upload_date'] . "</td>" .
            "<td>" . $media['status'] . "</td>" .
            "</tr>";
        }
        ?>
    </table>
</div>

