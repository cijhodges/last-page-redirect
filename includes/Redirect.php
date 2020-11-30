<?php
namespace LastPageRedirect;

class Redirect
{
    public
        $id,
        $referal_url,
        $operator,
        $date;

    public function __construct( $props = array() )
    {
        $this->set( $props );
    }

    public function check( $url ) {
        if ( !$this->referal_url ) return false;
        if ( !$this->operator || !$this->validate( 'operator', $this->operator ) ) return false;

        switch ( $this->operator ) {
            case 'exact match':
                if ( $this->referal_url === $url ) { 
                    return true;
                }
                break;

            case 'contains':
                if ( strpos( $this->referal_url, $url ) >= 0 ) {
                    return true;
                }
                break;

            default:
                return false;
                break;
        }
    }

    public function create()
    {
        if ( !$this->referal_url || !$this->operator ) return false;
        if ( !$this->validate( 'referal_url', $this->referal_url ) ) return false;
        if ( !$this->validate( 'operator', $this->operator ) ) return false;

        global $wpdb;

       $result = $wpdb->query(
            $wpdb->prepare( 
               "INSERT INTO `{$wpdb->prefix}last_page_redirect` SET `referal_url` = %s, `operator` = %s",
               array(
                    $this->referal_url,
                    $this->operator
                )
            )
       );

       if ( $result ) $this->id = $wpdb->insert_id;

       return $result;
    }

    public function delete()
    {
        if ( !$this->id ) return false;

        global $wpdb;

        $result = $wpdb->query(
            $wpdb->prepare( 
                "DELETE FROM `{$wpdb->prefix}last_page_redirect` WHERE `id` = %s",
                array( $this->id )
            )
        );

        if ( !$result ) return false;
        return true;
    }

    public function read()
    {
        if ( !$this->id ) return false;

        global $wpdb;

        $result = $wpdb->get_results(
            $wpdb->prepare( 
                "SELECT * FROM `{$wpdb->prefix}last_page_redirect` WHERE `id` = %s",
                array( $this->id )
            )
        );

        if ( count( $result ) === 0 ) return false;

        $this->set( $result[0] );
        return true;
    }

    public function set( $props = array() )
    {
        foreach ( $props as $key => $value ) {
            if ( $this->validate( $key, $value ) ) $this->$key = $value;
        }
    }

    public function update()
    {
        if ( !$this->id ) return false;
        if ( !$this->referal_url && !$this->operator ) return false;

        global $wpdb;
        $results = $wpdb->get_results( "SELECT * FROM `{$wpdb->prefix}last_page_redirect` WHERE `id` = {$this->id}" );

        if ( count( $results ) === 0 ) return false;

        $sql = "UPDATE `{$wpdb->prefix}last_page_redirect` SET ";

        $arr = array();
        $values = array();

        if ( $this->referal_url ) $arr[] = 'referal_url';
        if ( $this->operator ) $arr[] = 'operator';

        for ( $i = 0; $i < count( $arr ); $i++ ) {
            $key = $arr[$i];

            if ( $i !== 0 ) $sql .= ",";

            $sql .= " `$key` = %s";
            $values[] = $this->$key;
        }

        $sql .= " WHERE `id` = %s";

        $values[] = $this->id;

        return $wpdb->query(
            $wpdb->prepare( $sql, $values )
        );
    }

    public function validate( $key, $value )
    {
        if ( !property_exists( $this, $key ) ) {
            return false;
        }

        switch ( $key ) {
            case 'id':
                if ( !is_numeric( $value ) ) return false;
                break;

            case 'referal_url':
                if ( !is_string( $value ) ) return false;
                break;

            case 'operator':
                if ( !in_array( $value, array( 'exact match', 'contains' ) ) ) return false;
                break;

            case 'date':
                if ( \DateTime::createFromFormat('Y-m-d H:i:s', $value ) == false ) return false;
                break;

            default:
                return false;
                break;
        }

        return true;
    }
}
