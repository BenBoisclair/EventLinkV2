export function extractWebsiteId(pageProps: any): string | number | undefined {
    return pageProps?.websiteId || pageProps?.website?.id;
}
