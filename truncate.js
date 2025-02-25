document.addEventListener("DOMContentLoaded", function () {
    console.log("✅ Bluesky Truncate Script Loaded");

    customElements.whenDefined("bsky-embed").then(() => {
        console.log("✅ bsky-embed component is ready!");

        const embeds = document.querySelectorAll("bsky-embed");
        if (!embeds.length) {
            console.error("❌ No Bluesky embeds found.");
            return;
        }

        function truncatePosts(embed) {
            if (!embed.shadowRoot) {
                console.error("❌ bsky-embed shadowRoot not found.");
                return false;
            }

            const posts = embed.shadowRoot.querySelectorAll('[id^="post-"]');
            console.log(`🔍 Checking for posts... Found: ${posts.length}`);

            if (posts.length > 0) {
                console.log("✅ Bluesky posts found!");

                posts.forEach(post => {
                    if (!post) return;

                    if (post.querySelector(".truncated-content")) return;

                    const contentWrapper = document.createElement("div");
                    contentWrapper.className = "truncated-content";
                    contentWrapper.style.overflow = "hidden";
                    contentWrapper.style.maxHeight = "120px";
                    contentWrapper.style.transition = "max-height 0.3s ease-in-out";

                    while (post.firstChild) {
                        contentWrapper.appendChild(post.firstChild);
                    }

                    post.appendChild(contentWrapper);

                    const readMore = document.createElement("span");
                    readMore.className = "read-more";
                    readMore.textContent = "Read More";
                    readMore.style.cursor = "pointer";
                    readMore.style.color = "#ff4500";
                    readMore.style.display = "block";
                    readMore.style.marginTop = "5px";

                    readMore.addEventListener("click", () => {
                        if (contentWrapper.style.maxHeight === "120px") {
                            contentWrapper.style.maxHeight = "none";
                            readMore.textContent = "Show Less";
                        } else {
                            contentWrapper.style.maxHeight = "120px";
                            readMore.textContent = "Read More";
                        }
                    });

                    post.appendChild(readMore);
                });

                return true;
            }

            return false;
        }

        embeds.forEach(embed => {
            let attempts = 0;
            const interval = setInterval(() => {
                if (truncatePosts(embed) || attempts >= 20) {
                    clearInterval(interval);
                    if (attempts >= 20) console.error("❌ Bluesky posts did not load in time.");
                }
                attempts++;
            }, 500);
        });
    });
});
