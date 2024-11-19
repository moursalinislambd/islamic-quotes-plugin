// Import necessary dependencies
const { registerBlockType } = wp.blocks;
const { useBlockProps } = wp.blockEditor;

// Register the block
registerBlockType('islamic-quotes-plugin/quote-block', {
    title: 'Islamic Quote',
    icon: 'format-quote', // You can use different dashicons or SVG for the block icon
    category: 'widgets',
    edit: () => {
        // Return preview content in editor mode
        const blockProps = useBlockProps();
        return <div {...blockProps}>Islamic Quote will be displayed here (Preview)</div>;
    },
    save: () => {
        // Use shortcode to display the quote in the frontend
        return null;
    },
});
