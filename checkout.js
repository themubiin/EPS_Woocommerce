(function() {
    if ( ! window.wc || ! window.wc.wcBlocksRegistry || ! window.wc.wcSettings ) {
        return;
    }

    const settings = window.wc.wcSettings.getSetting( 'eps_data', {} ) || {};
    const { createElement } = window.wp.element;
    const { decodeEntities } = window.wp.htmlEntities || { decodeEntities: (s) => s };
    const { __ } = window.wp.i18n || { __: (s) => s };

    const titleStr = typeof settings.title === 'string' ? settings.title : 'Visa/Mastercard/MFS';
    const labelText = decodeEntities( titleStr ) || __( 'Visa/Mastercard/MFS', 'eps' );

    // Description with HTML (banner, etc.)
    const Content = () => {
        if ( ! settings.description ) {
            return null;
        }
        return createElement( 'div', {
            dangerouslySetInnerHTML: { __html: settings.description }
        } );
    };

    // Icon FIRST, then title
    const Label = () => {
        const elements = [];
        if ( settings.icon && typeof settings.icon === 'string' && settings.icon.length > 0 ) {
            elements.push(
                createElement( 'img', {
                    key: 'eps-logo',
                    src: settings.icon,
                    alt: 'EPS Logo',
                    style: { height: '24px', marginRight: '8px', verticalAlign: 'middle' }
                } )
            );
        }
        elements.push( createElement( 'span', { key: 'eps-label' }, labelText ) );

        return createElement(
            'span',
            { style: { display: 'inline-flex', alignItems: 'center' } },
            elements
        );
    };

    const features = ( settings && Array.isArray( settings.supports ) ) ? settings.supports : [ 'products' ];

    const Block_Gateway = {
        name: 'eps',
        label: createElement( Label ),
        content: createElement( Content ),
        edit: createElement( Content ),
        canMakePayment: () => true,
        ariaLabel: labelText,
        supports: {
            features: features,
        },
    };

    window.wc.wcBlocksRegistry.registerPaymentMethod( Block_Gateway );
})();
