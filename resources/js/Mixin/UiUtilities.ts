import { message } from 'ant-design-vue';
import { ref } from "vue";

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
    
    const notification = (page: {success: string, error: string}) => {
        if (page.success) {
            message.success({ content:  page.success, key: loadingMessageKey, duration: 3.5 });
        }
        
        if (page.error) {
            message.error({ content:  page.error, key: loadingMessageKey, duration: 3.5 });
        }
    }

    return {notification, onLoading};
}

export const onLoading = ref(false);

export function dashboardRoute() {
    const webRoute = route().current();
    const res = webRoute?.split(".")[0];
    return res + ".dashboard";
};
