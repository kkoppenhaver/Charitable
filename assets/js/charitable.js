jQuery.noConflict();

CHARITABLE = {};

CHARITABLE.Toggle = {

    toggleTarget : function( $el ) {
        var target = $el.data( 'charitable-toggle' );

        jQuery( '#' + target ).toggleClass( 'charitable-hidden', $el.is( ':checked' ) );

        return false;
    }, 

    hideTarget : function( $el ) {
        var target = $el.data( 'charitable-toggle' );

        jQuery( '#' + target ).addClass( 'charitable-hidden' );
    },

    init : function() {
        var self = this;        
        jQuery( '[data-charitable-toggle]' ).each( function() { 
            return self.hideTarget( jQuery( this ) ); 
        } )  
        .on( 'click', function( event ) {
            return self.toggleTarget( jQuery( this ) ); 
        } );
    }
};

/**
 * Donation amount selection
 */
CHARITABLE.DonationSelection = {

    selectOption : function( $el ) {
        var $input = $el.find( 'input[type=radio]' ), 
            checked = ! $input.is( ':checked' );

        $input.prop( 'checked', checked ); 

        if ( $el.hasClass( 'selected' ) ) {
            $el.removeClass( 'selected' );
            return false;
        }

        jQuery( '.donation-amount.selected ').removeClass( 'selected' );
        $el.addClass( 'selected' );

        if ( $el.hasClass( 'custom-donation-amount' ) ) {               
            $el.siblings( 'input[name=custom_donation_amount]' ).focus();
        }

        return false;
    },
    
    init : function() {
        var self = this;
        jQuery( '.donation-amount input[type=radio]' ).css( 'z-index', -1 );

        jQuery( '.donation-amount input:checked' ).each( function() {
            jQuery( this ).parent().addClass( 'selected' );
        });

        jQuery( 'body' ).on( 'click', '.donation-amount', function( event ) {
            self.selectOption( jQuery(this) );
        });

        jQuery( 'body' ).on( 'change', '[name=donation_amount]', function( event ) {
            jQuery(this).prop( 'checked', ! jQuery(this).is( ':checked' ) );
            return false;
        });
    }
};

/**
 * AJAX donation
 */
CHARITABLE.AJAXDonate = {

    onClick : function( event ) {
        var data = jQuery( event.target.form ).serializeArray().reduce( function( obj, item ) {
            obj[ item.name ] = item.value;
            return obj;
        }, {} );            

        /* Cancel the default Charitable action, but pass it along as the form_action variable */       
        data.action = 'add_donation';
        data.form_action = data.charitable_action;          
        delete data.charitable_action;

        jQuery.ajax({
            type: "POST",
            data: data,
            dataType: "json",
            url: CHARITABLE_VARS.ajaxurl,
            xhrFields: {
                withCredentials: true
            },
            success: function (response) {
            }
        }).fail(function (response) {
            if ( window.console && window.console.log ) {
                console.log( response );
            }
        }).done(function (response) {

        });

        return false;
    },

    init : function() {
        var self = this;
        jQuery( '[data-charitable-ajax-donate]' ).on ( 'click', function( event ) {
            return self.onClick( event );
        });
    }
};

/**
 * URL sanitization
 */
CHARITABLE.SanitizeURL = function(input) {
    var url = input.value.toLowerCase();

    if ( !/^https?:\/\//i.test( url ) ) {
        url = 'http://' + url;

        input.value = url;
    }
};

/**
 * Set up Lean Modal
 */
CHARITABLE.Modal = {
    init : function() {
        if ( jQuery.fn.leanModal ) {
            jQuery('[data-trigger-modal]').leanModal({
                closeButton : ".modal-close"
            });
        }
    }
};

/**
 * Payment method selection
 */
 CHARITABLE.PaymentMethodSelection = {

    loaded : false,

    getActiveMethod : function( $el ) {
        return jQuery( '#charitable-gateway-selector input[name=gateway]:checked' ).val();
    },

    hideInactiveMethods : function( active ) {
        var active = active || this.getActiveMethod();

        jQuery( '#charitable-gateway-fields .charitable-gateway-fields[data-gateway!=' + active + ']' ).hide();
    },

    showActiveMethod : function( active ) {
        jQuery( '#charitable-gateway-fields .charitable-gateway-fields[data-gateway=' + active + ']' ).show();
    },

    init : function() {
        var self = this, 
            $selector = jQuery( '#charitable-gateway-selector input[name=gateway]' );        

        /* If there is only one gateway, we don't need to do anything else. */
        if ( 0 === $selector.length ) {
            return;
        }

        self.hideInactiveMethods();

        if ( self.loaded ) {
            return;
        }

        jQuery( 'body' ).on( 'change', '#charitable-gateway-selector input[name=gateway]', function() {
            self.hideInactiveMethods();
            self.showActiveMethod( jQuery(this).val() );
        });

        self.loaded = true;
    }
 };


/**
 * Donation amount selection
 */
CHARITABLE.Accounting = {

    format_currency : function( price, currency_symbol ){

        if ( typeof currency_symbol === 'undefined' )
            currency_symbol = '';

        return accounting.formatMoney( price, {
                symbol : currency_symbol,
                decimal : CHARITABLE_VARS.currency_format_decimal_sep,
                thousand: CHARITABLE_VARS.currency_format_thousand_sep,
                precision : CHARITABLE_VARS.currency_format_num_decimals,
                format: CHARITABLE_VARS.currency_format  
        }).trim();

    },

    unformat_currency : function( price ){
        return Math.abs( parseFloat( accounting.unformat( price, CHARITABLE_VARS.currency_format_decimal_sep ) ) );
    },
    
    init : function() {
        var self = this;

        jQuery( 'body' ).on( 'blur', '[name=custom_donation_amount]', function( event ) {
            var value_now = self.unformat_currency( jQuery( this ).val() );
            var formatted_total = self.format_currency( value_now );
            jQuery( this ).val( formatted_total );
        });
    }
};

(function() {
    jQuery( document ).ready( function() {
        CHARITABLE.Toggle.init();

        CHARITABLE.DonationSelection.init();
        
        CHARITABLE.AJAXDonate.init();       

        CHARITABLE.PaymentMethodSelection.init();

        CHARITABLE.Modal.init();

        CHARITABLE.Accounting.init();
    });
})();