const CustomPopover = (function ($) {
    let popoverElement = null;
    let currentTrigger = null;
    let onSaveCallback = null;

    function createPopover(text, position) {
        return $('<div>', {
            class: 'custom-popover',
            html: `
                <textarea class="popover-textarea form-control">${text}</textarea>
                <div class="popover-buttons mt-2">
                    <button class="btn btn-success btn-sm btn-save">Save</button>
                <button class="btn btn-secondary btn-sm btn-cancel">Cancel</button>  
                </div>
            `
        }).css({
            position: 'absolute',
            top: `${position.top}px`,
            left: `${position.left}px`,
            zIndex: 9999
        });
    }

    function showPopover(trigger, text, position) {
        const prevTrigger = currentTrigger;
        const prevPopover = popoverElement;

        if (prevPopover && prevTrigger) {
            // Save previous content before opening new one
            const newText = prevPopover.find('.popover-textarea').val();
            if (onSaveCallback) {
                onSaveCallback(prevTrigger, newText);
            }
            prevPopover.remove();
        }

        currentTrigger = trigger;
        popoverElement = createPopover(text, position);
        $('body').append(popoverElement);

        popoverElement.find('.btn-save').on('click', handleSave);
        popoverElement.find('.btn-cancel').on('click', hidePopover);
        popoverElement.find('.popover-textarea').focus();

        $('body').addClass('popover-open');
    }

    function handleSave() {
        const newText = popoverElement.find('.popover-textarea').val();
        if (onSaveCallback) {
            onSaveCallback(currentTrigger, newText);
        } else {
            console.log("Did not have a save callback: ", newText);
        }
        hidePopover();
    }

    function hidePopover() {
        if (popoverElement) {
            popoverElement.remove();
            popoverElement = null;
        }
        currentTrigger = null;
        $('body').removeClass('popover-open');
    }

    function isClickFarAway(clickEvent, textareaElement) {
        const textareaRect = textareaElement[0].getBoundingClientRect();
        const textareaCenterX = textareaRect.left + (textareaRect.width / 2);
        const textareaCenterY = textareaRect.top + (textareaRect.height / 2);
        const clickX = clickEvent.clientX;
        const clickY = clickEvent.clientY;

        const distance = Math.sqrt(
            Math.pow(clickX - textareaCenterX, 2) +
            Math.pow(clickY - textareaCenterY, 2)
        );

        return distance >= 200;
    }

    function handleGlobalClick(e) {
        if ($('body').hasClass('popover-open')) {
            const isClickInsidePopover = popoverElement && $.contains(popoverElement[0], e.target);
            const isClickOnTrigger = currentTrigger && $.contains(currentTrigger, e.target);
            const textareaElement = popoverElement.find('.popover-textarea');

            if (!isClickInsidePopover && !isClickOnTrigger && isClickFarAway(e, textareaElement)) {
                //hidePopover();
                handleSave();
            }
        }
    }

    function init(selector, options = {}) {
        onSaveCallback = options.onSave || null;
        console.log("Popover Inititaion... ");
        $(document).on('click', function (e) {
            const trigger = $(e.target).closest(selector);
            if (trigger.length) {
                e.stopPropagation();
                const text = trigger.data('description');
                const rect = trigger[0].getBoundingClientRect();

                showPopover(trigger[0], text, {
                    top: rect.bottom + window.scrollY,
                    left: rect.left + window.scrollX
                });
            } else {
                handleGlobalClick(e);
            }
        });
    }

    return {
        init: init,
        hide: hidePopover
    };
})(jQuery);

export default CustomPopover;
