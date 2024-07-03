import { message } from 'ant-design-vue';

export function highlighten() {
    const highlightText = (text: string, searchQuery: any) => {
        if (!searchQuery) return text;
        if (text != null) {
            const regex = new RegExp(searchQuery, "gi");
            return text
                .toString()
                .replace(
                    regex,
                    (match) =>
                        `<span style="background-color: yellow">${match}</span>`
                );
        }
    };

    return { highlightText };
}

export function onProgress(){
    const loadingMessageKey = 'loadingMessage';

    const onLoading = () => {
        message.loading({ content: 'Action in progress..', key: loadingMessageKey, duration: 0 });
    } 
    
    const notification = (page) => {
        if (page.props.flash.success) {
            message.success({ content:  page.props.flash.success, key: loadingMessageKey, duration: 3.5 });
        }
        
        if (page.props.flash.error) {
            message.error({ content:  page.props.flash.error, key: loadingMessageKey, duration: 3.5 });
        }
    }

    return {notification, onLoading};
}
