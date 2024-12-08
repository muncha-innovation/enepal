<style>
    #container {
        width: 1000px;
        margin: 20px auto;
    }

    .ck-editor__editable[role="textbox"] {
        /* Editing area */
        min-height: 200px;
        padding: 0 1rem !important;  /* Add padding to prevent list markers from being cut off */
    }

    .ck-content .image {
        /* Block images */
        max-width: 80%;
        margin: 20px auto;
    }

    /* Fix for bullet points and numbering */
    .ck-content ol {
        /* list-style-position: inside; */
        padding-left: 20px;
    }

    .ck-content ul {
        /* list-style-position: inside; */
        padding-left: 20px;
    }

    /* Ensure nested lists are properly indented */
    .ck-content ol ol,
    .ck-content ul ul,
    .ck-content ul ol,
    .ck-content ol ul {
        padding-left: 30px;
    }
</style>