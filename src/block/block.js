/**
 * BLOCK: post-loop
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

//  Import CSS.
import './editor.scss';
import './style.scss';

const { __ } = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks

/**
 * Register: aa Gutenberg Block.
 *
 * Registers a new block provided a unique name and an object defining its
 * behavior. Once registered, the block is made editor as an option to any
 * editor interface where blocks are implemented.
 *
 * @link https://wordpress.org/gutenberg/handbook/block-api/
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */


registerBlockType( 'cgb/block-post-loop', {
	// Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
	title: __( 'post-loop - KOD sidebar' ), // Block title.
	icon: 'text-page', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
	category: 'common', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
	keywords: [
		__( 'post-loop — KOD sidebar' ),
		__( 'CGB Example' ),
		__( 'create-guten-block' ),
	],

	//Defining attributes
	attributes: {
		categories: {
			type: 'object'
		},

		selectedCategory: {
			type: 'string'
		},

		postPerPage: {
			type: 'string'
		}
	},
	

	edit: ( props ) => {
		// Creates a <p class='wp-block-cgb-block-post-loop'></p>.
		if( ! props.attributes.categories) {
			wp.apiFetch({
				//Fetch all categories with per_page=-1  -  deafault is 10.
				url: '/wp-json/wp/v2/categories?per_page=-1'
			}

			).then( categories => {
				props.setAttributes(
					{
					categories: categories
				}
				)
			})
		}

		if ( ! props.attributes.categories ){
			return 'Loading....'
		}

		if ( props.attributes.categories && props.attributes.categories.length == 0 ){
			return 'Add some...'
		}

		function updateCategory(e){
			props.setAttributes({
				selectedCategory: e.target.value,
			});
		}

		function postPerPage(e){
			props.setAttributes({
				postPerPage: e.target.value,
			});
		}

		return (
			<div class="block-wrapper">
				<label class="label-block">Izaberite kategoriju</label>
				<select class="select-block" onChange={ updateCategory } value={ props.attributes.selectedCategory }>
					{
						props.attributes.categories.map( category => {
							return (
								<option value={ category.id } key= { category.id }> 
								{ category.name }
								</option>
							);
						})
					}
				</select>
				<label class="label-block">Unesite broj za broj postova koji će se prikazati</label>	
				<input class="text-block" type="text" onChange={ postPerPage } value={ props.attributes.postPerPage }/>
					
				
				
			</div>
		);
	},


	save: ( props ) => {
		return (
			null
		);
	},
} );
