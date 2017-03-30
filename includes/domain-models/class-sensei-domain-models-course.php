<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Class Sensei_Domain_Models_Course
 * @package Domain_Models
 */
class Sensei_Domain_Models_Course extends Sensei_Domain_Models_Model_Abstract {

    public static function declare_fields() {
        return array(
            self::field()
                ->with_name( 'id' )
                ->map_from( 'ID' )
                ->with_value_type('integer')
                ->with_description( __( 'Unique identifier for the object.', 'woothemes-sensei' ) )
                ->with_before_return( 'as_uint' ),
            self::field()
                ->with_name( 'title' )
                ->map_from( 'post_title' )
                ->with_value_type('string')
                ->with_description( __( 'The course title.', 'woothemes-sensei' ) )
                ->required( true ),
            self::field()
                ->with_name( 'author' )
                ->map_from( 'post_author' )
                ->with_value_type('integer')
                ->with_description( __( 'The author identifier.', 'woothemes-sensei' ) )
                ->with_before_return( 'as_uint' ),
            self::field()
                ->with_name( 'content' )
                ->with_value_type('string')
                ->with_description( __( 'The course content.', 'woothemes-sensei' ) )
                ->map_from( 'post_content' ),
            self::field()
                ->with_name( 'excerpt' )
                ->with_value_type('string')
                ->with_description( __( 'The course excerpt.', 'woothemes-sensei' ) )
                ->map_from( 'post_excerpt' ),
            self::field()
                ->with_name( 'type' )
                ->with_value_type('string')
                ->with_default_value( 'course' )
                ->map_from( 'post_type' ),
            self::field()
                ->with_name( 'status' )
                ->with_value_type('string')
                ->with_description( __( 'The course status.', 'woothemes-sensei' ) )
                ->map_from( 'post_status' ),

            self::derived_field()
                ->with_name( 'modules' )
                ->map_from( 'course_module_ids' )
                ->with_description( __( 'The course module ids.', 'woothemes-sensei' ) )
                ->with_json_name( 'module_ids' ),
            self::derived_field()
                ->with_name( 'module_order' )
                ->with_description( __( 'The course module id order.', 'woothemes-sensei' ) )
                ->map_from( 'module_order' ),
            self::derived_field()
                ->with_name( 'lessons' )
                ->with_description( __( 'The course lessons.', 'woothemes-sensei' ) )
                ->map_from( 'course_lessons' )
                ->not_visible(),

            self::meta_field()
                ->with_name( 'prerequisite' )
                ->map_from( '_course_prerequisite' )
                ->with_description( __( 'The course prerequisite.', 'woothemes-sensei' ) )
                ->with_before_return( 'as_nullable_uint' ),
            self::meta_field()
                ->with_name( 'featured' )
                ->map_from( '_course_featured' )
                ->with_description( __( 'Is the course featured.', 'woothemes-sensei' ) )
                ->with_value_type('boolean')
                ->with_before_return( 'as_bool' )
                ->with_json_name( 'is_featured' ),
            self::meta_field()
                ->with_name( 'video_embed' )
                ->with_description( __( 'The course video embed html.', 'woothemes-sensei' ) )
                ->map_from( '_course_video_embed' ),
            self::meta_field()
                ->with_name( 'woocommerce_product' )
                ->map_from( '_course_woocommerce_product' )
                ->with_description( __( 'The product associated with this course.', 'woothemes-sensei' ) )
                ->with_json_name( 'woocommerce_product_id' )
                ->with_before_return( 'as_nullable_uint' ),
            self::meta_field()
                ->with_name( 'lesson_order' )
                ->map_from( '_lesson_order' )
        );
    }

    public function get_id() {
        return $this->id;
    }

    protected function course_module_ids() {
        $modules = Sensei()->modules->get_course_modules( absint( $this->id ) );
        return array_map( 'absint', wp_list_pluck( $modules, 'term_id' ) );
    }

    /**
     * Get module order callable
     * @return array
     */
    protected function module_order() {
        $modules = Sensei()->modules->get_course_module_order( absint( $this->id ) );
        return ( empty( $modules ) ) ? array() : array_map( 'absint', $modules );
    }

    /**
     * validate object
     * @return bool|WP_Error
     */
    public function validate() {
        $validation_errors = array();
        $title = $this->title;
        if ( empty( $title ) ) {
            $validation_errors[] = new WP_Error(
                'empty_title',
                __( 'title cannot be empty', 'woothemes-sensei' )
            );
        }
        if ( count( $validation_errors ) > 0 ) {
            return $this->validation_error( $validation_errors );
        }
        return true;
    }

    /**
     * @param $error_data array
     * @return WP_Error
     */
    protected function validation_error( $error_data ) {
        return new WP_Error( 'validation-error',  __( 'Validation Error', 'woothemes-sensei' ), $error_data );
    }
}