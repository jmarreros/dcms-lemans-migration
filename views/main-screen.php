<section id="section-advanced">
    <h2>Migration</h2>
    <hr>

    <table class="form-table init-categories">
        <tbody>
        <tr id="init-categories">
            <th scope="row">
				<?php _e( 'Categories initialization', 'lemans-migration' ) ?>
            </th>
            <td>
                <button class="button button-primary">
					<?php _e( 'Migrate categories', 'lemans-migration' ); ?>
                </button>
                <span class="msg-btn"></span>
                <div class="loading hide"></div>
            </td>
        </tr>
        </tbody>
    </table>

    <hr>

    <table class="form-table migrate-products">
        <tbody>
        <tr id="migrate-products">
            <th scope="row">
				<?php _e( 'Migrate products', 'lemans-migration' ) ?>
            </th>
            <td>
                <div style="margin-bottom: 10px">Subir el archivo <strong>data.csv</strong> a la raíz del sitio</div>
                <button id='process-migration-products' class="button button-primary">
					<?php _e( 'Migrate products', 'lemans-migration' ); ?>
                </button>

                <div class="process-info" style="margin-top:10px"></div>

            </td>
        </tr>
        </tbody>
    </table>

    <hr>

    <table class="form-table related-products">
        <tbody>
        <tr id="related-products">
            <th scope="row">
				<?php _e( 'Related products', 'lemans-migration' ) ?>
            </th>
            <td>
                <button id='process-related-products' class="button button-primary">
					<?php _e( 'Related products', 'lemans-migration' ); ?>
                </button>

                <div class="process-info" style="margin-top:10px"></div>

            </td>
        </tr>
        </tbody>
    </table>
</section>