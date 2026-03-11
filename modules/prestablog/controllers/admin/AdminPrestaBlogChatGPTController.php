<?php
/**
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminPrestaBlogChatGPTController extends ModuleAdminController
{
    public function initContent()
    {
        if (!$this->viewAccess()) {
            $this->errors[] = Tools::displayError('You do not have permission to view this.');

            return;
        }

        $id_tab = (int) Tab::getIdFromClassName('AdminModules');
        $id_employee = (int) $this->context->cookie->id_employee;
        $token = Tools::getAdminToken('AdminModules' . $id_tab . $id_employee);
        Tools::redirectAdmin('index.php?controller=AdminModules&configure=prestablog&token=' . $token);
    }

    public function ajaxProcessChatWithGpt()
    {
        $rawData = Tools::file_get_contents('php://input');
        $data = json_decode($rawData, true);

        if (!isset($data['prompt']) || empty($data['prompt'])) {
            exit(json_encode(['success' => false, 'message' => $this->trans('This field cannot be empty', [], 'Modules.Prestablog.Ai')]));
        }

        if ($data['prompt'] === 'free_discussion' && (!isset($data['message']) || empty($data['message']))) {
            exit(json_encode(['success' => false, 'message' => $this->trans('This field cannot be empty', [], 'Modules.Prestablog.Ai')]));
        }

        $message = isset($data['message']) ? $data['message'] : '';
        $theme = isset($data['theme']) ? htmlspecialchars($data['theme'], ENT_QUOTES, 'UTF-8') : '';
        $prompt = htmlspecialchars($data['prompt'], ENT_QUOTES, 'UTF-8');
        $style = isset($data['style']) ? htmlspecialchars($data['style'], ENT_QUOTES, 'UTF-8') : '';

        $promptsRequiringTheme = ['find_topic', 'seo_variation', 'write_article', 'create_summary', 'create_title', 'create_meta_title', 'create_meta_description'];
        if (in_array($prompt, $promptsRequiringTheme) && empty($theme)) {
            exit(json_encode(['success' => false, 'message' => $this->trans('This field cannot be empty', [], 'Modules.Prestablog.Ai')]));
        }

        if ($prompt === 'find_topic') {
            $message = $this->trans(
                "I manage a website focused on the theme of %theme%. Can you suggest some blog topics I should develop? Format the response as an HTML table with two columns: one for the 'Topic' and one for the 'Popularity Score' (a number between 1 and 100). Each topic should be in a separate row.",
                ['%theme%' => $theme],
                'Modules.Prestablog.Ai'
            );
        } elseif ($prompt === 'seo_variation') {
            $message = $this->trans(
                'Use the topic "%theme%". Create a table with title and keyword variations that group long-tail terms capable of ranking in search engines. Format the response as an HTML table with two columns: one for the Titles and one for the keywords.',
                ['%theme%' => $theme],
                'Modules.Prestablog.Ai'
            );
        } elseif ($prompt === 'write_article') {
            $message = $this->trans(
                'I want you to write a SEO-optimized blog article on the topic of "%theme%", with a writing style of "%style%". Structure the text in HTML with appropriate tags. The article should be engaging, relevant, high-quality, and undetectable as AI-generated content. Start with an introductory paragraph without mentioning the blog or using a heading. Use subheadings formatted with h2 and h3 tags, incorporating long-tail titles to enhance SEO. Naturally integrate long-tail keywords and highlight them with strong tags. Ensure that each section contains at least 100 words, with smooth transitions. Conclude with a summary that reinforces the key points without adding new information.',
                ['%theme%' => $theme, '%style%' => $style],
                'Modules.Prestablog.Ai'
            );
        } elseif ($prompt === 'create_summary') {
            $message = $this->trans(
                'Create a captivating introduction that will be displayed in the blog category to present the article "%theme%". This introduction should be concise, engaging, and provide a general overview of the article\'s content.',
                ['%theme%' => $theme],
                'Modules.Prestablog.Ai'
            );
        } elseif ($prompt === 'create_title') {
            $message = $this->trans(
                'Based on the topic "%theme%", create a compelling title that will capture attention and summarize the content effectively. And do not include quotation marks around your answer',
                ['%theme%' => $theme],
                'Modules.Prestablog.Ai'
            );
        } elseif ($prompt === 'create_meta_title') {
            $message = $this->trans(
                'Based on the topic "%theme%", create a short and effective meta title that is SEO-friendly and accurately represents the content. And do not include quotation marks around your answer',
                ['%theme%' => $theme],
                'Modules.Prestablog.Ai'
            );
        } elseif ($prompt === 'create_meta_description') {
            $message = $this->trans(
                'Based on the topic "%theme%", create a concise and compelling meta description that encourages clicks and is optimized for search engines. And do not include quotation marks around your answer',
                ['%theme%' => $theme],
                'Modules.Prestablog.Ai'
            );
        }

        $response = $this->module->sendMessageToGpt($message);

        if ($response['success']) {
            exit(json_encode(['success' => true, 'response' => $response['response']]));
        } else {
            exit(json_encode(['success' => false, 'message' => $response['message']]));
        }
    }

    public function ajaxProcessTranslateMessage()
    {
        $rawData = Tools::file_get_contents('php://input');
        $data = json_decode($rawData, true);

        if (!isset($data['message']) || empty($data['message']) || !isset($data['language']) || empty($data['language'])) {
            exit(json_encode(['success' => false, 'message' => $this->trans('This field cannot be empty', [], 'Modules.Prestablog.Ai')]));
        }

        $message = $data['message'];
        $language = htmlspecialchars($data['language'], ENT_QUOTES, 'UTF-8');
        $response = $this->module->translateMessageWithGpt($message, $language);

        if ($response['success']) {
            exit(json_encode(['success' => true, 'translation' => $response['translation']]));
        } else {
            exit(json_encode(['success' => false, 'message' => $response['message']]));
        }
    }
}
