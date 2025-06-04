document.addEventListener("DOMContentLoaded", function () {
    try {
        // Skip in iframes or admin
        if (window.location !== window.parent.location || document.body.classList.contains('wp-admin')) {
            return;
        }

        console.log("✅ Bluesky Truncate Script Loaded");

        customElements.whenDefined("bsky-embed").then(() => {
            console.log("✅ bsky-embed component is ready!");

            const embeds = document.querySelectorAll("bsky-embed");
            if (!embeds.length) {
                console.warn("❌ No Bluesky embeds found.");
                return;
            }

            function truncatePosts(embed) {
                if (!embed.shadowRoot) return false;

                const posts = embed.shadowRoot.querySelectorAll('[id^="post-"]');
                if (!posts.length) return false;

                posts.forEach(post => {
                    if (!post || post.querySelector(".truncated-content")) return;

                    const wrapper = document.createElement("div");
                    wrapper.className = "truncated-content";
                    wrapper.style.overflow = "hidden";
                    wrapper.style.maxHeight = "120px";
                    wrapper.style.transition = "max-height 0.3s ease-in-out";

                    while (post.firstChild) {
                        wrapper.appendChild(post.firstChild);
                    }
                    post.appendChild(wrapper);

                    const toggle = document.createElement("span");
                    toggle.className = "read-more";
                    toggle.textContent = "Read More";
                    toggle.style.cursor = "pointer";
                    toggle.style.color = "#ff4500";
                    toggle.style.display = "block";
                    toggle.style.marginTop = "5px";

                    toggle.addEventListener("click", () => {
                        const expanded = wrapper.style.maxHeight !== "120px";
                        wrapper.style.maxHeight = expanded ? "120px" : "none";
                        toggle.textContent = expanded ? "Read More" : "Show Less";
                    });

                    post.appendChild(toggle);
                });

                return true;
            }

            embeds.forEach(embed => {
                let tries = 0;
                const interval = setInterval(() => {
                    if (truncatePosts(embed) || ++tries >= 20) clearInterval(interval);
                }, 500);
            });
        });
    } catch (e) {
        console.error("Bluesky Truncate Error:", e);
    }
});
