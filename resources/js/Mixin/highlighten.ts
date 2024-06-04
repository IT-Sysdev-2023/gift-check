
export function highlighten() {
    const highlightText = (text: string, searchQuery: any) => {
        if (!searchQuery) return text;
        if (text != null) {
            const regex = new RegExp(searchQuery, "gi");
            return text.replace(
                regex,
                (match) =>
                    `<span style="background-color: yellow">${match}</span>`
            );
        }
    };

    return { highlightText };
}
