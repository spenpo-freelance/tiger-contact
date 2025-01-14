<?php
namespace TigreGrades\Contact\Shortcodes;

use DOMDocument;
use DOMElement;

/**
 * Handles the [spenpo_resume] shortcode functionality.
 * 
 * @package Spenpo\Resume
 * @since 1.0.0
 */
class TigrContactShortcode {
    public function __construct() {
        // Register the REST API endpoint
        add_action('rest_api_init', [$this, 'register_rest_routes']);

        add_shortcode('tigr_contact', [$this, 'tigrRender']);
    }

    public function tigrRender() {
        // Create form with more fields
        $dom = new DOMDocument('1.0', 'utf-8');
        $form = $this->tigrCreateElement($dom, 'form', 'form');
        
        // Username field
        $form->appendChild($this->tigrCreateElement($dom, 'label', 'label', null, 'Username', ['for' => 'username']));
        $form->appendChild($this->tigrCreateElement($dom, 'input', 'input', null, null, ['type' => 'text', 'name' => 'username', 'required' => 'required']));
        
        // First name field
        $form->appendChild($this->tigrCreateElement($dom, 'label', 'label', null, 'First Name', ['for' => 'first_name']));
        $form->appendChild($this->tigrCreateElement($dom, 'input', 'input', null, null, ['type' => 'text', 'name' => 'first_name', 'required' => 'required']));
        
        // Last name field
        $form->appendChild($this->tigrCreateElement($dom, 'label', 'label', null, 'Last Name', ['for' => 'last_name']));
        $form->appendChild($this->tigrCreateElement($dom, 'input', 'input', null, null, ['type' => 'text', 'name' => 'last_name', 'required' => 'required']));
        
        // Email field
        $form->appendChild($this->tigrCreateElement($dom, 'label', 'label', null, 'Email', ['for' => 'email']));
        $form->appendChild($this->tigrCreateElement($dom, 'input', 'input', null, null, ['type' => 'email', 'name' => 'email', 'required' => 'required']));
        
        $form->appendChild($this->tigrCreateElement($dom, 'button', 'button', null, 'Submit', ['type' => 'submit']));

        // Update form attributes
        $form->setAttribute('id', 'inactive-user-form');
        // Remove the action and method attributes since we'll handle this with JS
        
        // Add response message div
        $messageDiv = $this->tigrCreateElement($dom, 'div', 'div', 'form-message');
        $form->appendChild($messageDiv);

        $dom->appendChild($form);

        // Add JavaScript for form handling
        $script = $dom->createElement('script');
        $script->textContent = '
            document.getElementById("inactive-user-form").addEventListener("submit", function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const data = {};
                formData.forEach((value, key) => data[key] = value);

                fetch("/wp-json/tigr/v1/submit", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    const messageDiv = document.querySelector(".form-message");
                    if (data.message) {
                        messageDiv.textContent = data.message;
                        messageDiv.style.color = "green";
                        e.target.reset(); // Clear form on success
                    } else if (data.code) {
                        messageDiv.textContent = data.message;
                        messageDiv.style.color = "red";
                    }
                })
                .catch(error => {
                    const messageDiv = document.querySelector(".form-message");
                    messageDiv.textContent = "An error occurred. Please try again.";
                    messageDiv.style.color = "red";
                });
            });
        ';

        $dom->appendChild($script);
        return $dom->saveHTML();
    }

    // Add this new method
    public function register_rest_routes() {
        register_rest_route('tigr/v1', '/submit', array(
            'methods' => 'POST',
            'callback' => [$this, 'create_inactive_user'],
            'permission_callback' => '__return_true'
        ));
    }

    // Move the callback function to be a class method
    public function create_inactive_user($request) {
        $body = json_decode($request->get_body(), true);
        
        // Create user
        $userdata = array(
            'user_login' => $body['username'],
            'user_email' => $body['email'],
            'user_pass'  => wp_generate_password(),
            'first_name' => $body['first_name'],
            'last_name'  => $body['last_name'],
            'role'       => 'subscriber'
        );

        $user_id = wp_insert_user($userdata);

        if (is_wp_error($user_id)) {
            return new WP_Error('user_creation_failed', $user_id->get_error_message(), array('status' => 400));
        }

        // Set user as inactive
        update_user_meta($user_id, 'account_status', 'inactive');

        return new WP_REST_Response(['message' => 'User created successfully'], 200);
    }

    /**
     * Creates a new DOM element with specified attributes.
     * 
     * @param DOMDocument $dom       The DOM document instance
     * @param string      $tag       HTML tag name
     * @param string      $class     CSS class name
     * @param string|null $id        Optional element ID
     * @param string|null $text      Optional text content
     * @param array       $attributes Optional additional attributes
     * 
     * @return DOMElement The created element
     */
    private function tigrCreateElement(DOMDocument $dom, $tag, $class, $id = null, $text = null, $attributes = []) {
        $element = $dom->createElement($tag);
        $element->setAttribute('class', $class);
        
        if ($id) {
            $element->setAttribute('id', $class."-$id");
        }

        foreach ($attributes as $key => $value) {
            $element->setAttribute($key, $value);
        }
        
        if ($text) {
            $element_text = $dom->createTextNode($text);
            $element->appendChild($element_text);
        }

        return $element;
    }
}

// Initialize shortcode
new TigrContactShortcode(); 