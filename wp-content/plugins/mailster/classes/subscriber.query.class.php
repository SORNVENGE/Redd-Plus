<?php

class MailsterSubscriberQuery {

	private $last_result;
	private $last_error;
	private $last_query;

	private $args = array();

	private $defaults = array(
		'select' => null,
		'status' => null,
		'status__not_in' => null,
		'where' => null,
		'having' => null,
		'orderby' => null,
		'order' => null,
		'limit' => null,
		'offset' => null,

		'return_ids' => false,
		'return_count' => false,
		'return_sql' => false,

		'operator' => null,
		'conditions' => null,

		'include' => null,
		'exclude' => null,

		'fields' => null,
		'meta' => null,

		'lists' => false,
		'lists__not_in' => null,

		'unsubscribe' => null,
		'unsubscribe__not_in' => null,

		'queue' => false,
		'queue__not_in' => false,

		's' => null,
		'search_fields' => false,
		'strict' => false,
		'sentence' => false,

		'calc_found_rows' => false,

		'signup_after' => null,
		'signup_before' => null,
		'confirm_after' => null,
		'confirm_before' => null,

		'sent' => null,
		'sent__not_in' => null,
		'sent_before' => null,
		'sent_after' => null,
		'open' => null,
		'open__not_in' => null,
		'open_before' => null,
		'open_after' => null,
		'click' => null,
		'click__not_in' => null,
		'click_before' => null,
		'click_after' => null,
		'click_link' => null,
		'click_link__not_in' => null,
	);

	private static $_instance = null;

	private function __construct( $args = null ) {

		if ( ! is_null( $args ) ) {
			return $this->run( $args );
		}

	}
	public function __destruct() {}

	public static function get_instance( $args = null ) {
		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __get( $name ) {

		if ( ! isset( $this->$name ) ) {
			$this->{$name} = $this->{'get_' . $name}();
		}

		return $this->{$name};

	}

	public function run( $args = array() ) {

		global $wpdb;

		if ( is_string( $args ) ) {
			$args = str_replace( '+', '%2B', $args );
		}

		$this->args = wp_parse_args( $args, $this->defaults );

		$name_order = mailster_option( 'name_order' );
		$joins = array();
		$wheres = array();

		if ( 'all' == $this->args['fields'] ) {
			$this->args['fields'] = $this->custom_fields;
			array_unshift( $this->args['fields'], 'fullname' );
			if ( ! $this->args['select'] ) {
				$this->args['select'] = array( 'subscribers.*' );
			}
		}
		if ( 'all' == $this->args['meta'] ) {
			$this->args['meta'] = $this->meta_fields;
		}

		if ( $this->args['return_ids'] ) {
			$this->args['select'] = array( 'subscribers.ID' );
		} elseif ( $this->args['return_count'] ) {
			$this->args['select'] = array( 'COUNT(DISTINCT subscribers.ID)' );
			$this->args['fields'] = null;
			$this->args['meta'] = null;
		} elseif ( empty( $this->args['fields'] ) && empty( $this->args['select'] ) ) {
			$this->args['select'] = array( 'subscribers.*' );
		} elseif ( is_null( $this->args['select'] ) ) {
			$this->args['select'] = array();
		}

		if ( $this->args['status'] !== false && is_null( $this->args['status'] ) ) {
			if ( ! $this->args['s'] ) {
				$this->args['status'] = array( 1 );
			}
		}

		if ( $this->args['status'] !== false && ! is_null( $this->args['status'] ) && ! is_array( $this->args['status'] ) ) {
			$this->args['status'] = explode( ',', $this->args['status'] );
		}

		if ( $this->args['status__not_in'] !== false && ! is_null( $this->args['status__not_in'] ) && ! is_array( $this->args['status__not_in'] ) ) {
			$this->args['status__not_in'] = explode( ',', $this->args['status__not_in'] );
		}

		if ( $this->args['include'] && ! is_array( $this->args['include'] ) ) {
			$this->args['include'] = explode( ',', $this->args['include'] );
		}
			$this->args['include'] = $this->id_parse( $this->args['include'] );

		if ( $this->args['exclude'] && ! is_array( $this->args['exclude'] ) ) {
			$this->args['exclude'] = explode( ',', $this->args['exclude'] );
		}
			$this->args['exclude'] = $this->id_parse( $this->args['exclude'] );

		if ( $this->args['select'] && ! is_array( $this->args['select'] ) ) {
			$this->args['select'] = explode( ',', $this->args['select'] );
		}
		if ( $this->args['where'] && ! is_array( $this->args['where'] ) ) {
			$this->args['where'] = array( $this->args['where'] );
		}
		if ( $this->args['fields'] && ! is_array( $this->args['fields'] ) ) {
			$this->args['fields'] = explode( ',', $this->args['fields'] );
		}
		if ( $this->args['meta'] && ! is_array( $this->args['meta'] ) ) {
			$this->args['meta'] = explode( ',', $this->args['meta'] );
		}
		if ( 'OR' != $this->args['operator'] ) {
			$this->args['operator'] = 'AND' === strtoupper( $this->args['operator'] ) ? 'AND' : 'OR' ;
		}
		if ( $this->args['orderby'] && ! is_array( $this->args['orderby'] ) ) {
			$this->args['orderby'] = explode( ',', $this->args['orderby'] );
		}
		if ( $this->args['order'] && ! is_array( $this->args['order'] ) ) {
			$this->args['order'] = explode( ',', $this->args['order'] );
		}
		if ( $this->args['queue'] && ! is_array( $this->args['queue'] ) ) {
			$this->args['queue'] = explode( ',', $this->args['queue'] );
		}
		if ( $this->args['queue__not_in'] && ! is_array( $this->args['queue__not_in'] ) ) {
			$this->args['queue__not_in'] = explode( ',', $this->args['queue__not_in'] );
		}
		if ( $this->args['lists'] && $this->args['lists'] !== true && ! is_array( $this->args['lists'] ) ) {
			$this->args['lists'] = explode( ',', $this->args['lists'] );
		}
		if ( $this->args['lists__not_in'] && ! is_array( $this->args['lists__not_in'] ) ) {
			$this->args['lists__not_in'] = explode( ',', $this->args['lists__not_in'] );
		}
		if ( $this->args['unsubscribe'] && ! is_array( $this->args['unsubscribe'] ) ) {
			$this->args['unsubscribe'] = explode( ',', $this->args['unsubscribe'] );
		}
		if ( $this->args['unsubscribe__not_in'] && ! is_array( $this->args['unsubscribe__not_in'] ) ) {
			$this->args['unsubscribe__not_in'] = explode( ',', $this->args['unsubscribe__not_in'] );
		}
		if ( $this->args['search_fields'] && ! is_array( $this->args['search_fields'] ) ) {
			$this->args['search_fields'] = explode( ',', $this->args['search_fields'] );
		}
		if ( $this->args['conditions'] ) {

			 $this->args['conditions'] = array_values( $this->args['conditions'] );

			// sanitize
			if ( ! isset( $this->args['conditions'][0][0] ) ) {
				if ( 'OR' == $this->args['operator'] ) {
					$this->args['conditions'] = array( $this->args['conditions'] );
				} else {
					$c = array();
					foreach ( $this->args['conditions'] as $cond ) {
						$c[] = array( $cond );
					}
					$this->args['conditions'] = $c;
				}
			}
		}

		if ( $this->args['signup_after'] ) {
			$this->add_condition( 'signup', '>=', $this->get_timestamp( $this->args['signup_after'] ) );
		}
		if ( $this->args['signup_before'] ) {
			$this->add_condition( 'signup', '<=', $this->get_timestamp( $this->args['signup_before'] ) );
		}
		if ( $this->args['confirm_after'] ) {
			$this->add_condition( 'confirm', '>=', $this->get_timestamp( $this->args['confirm_after'] ) );
		}
		if ( $this->args['confirm_before'] ) {
			$this->add_condition( 'confirm', '<=', $this->get_timestamp( $this->args['signup_before'] ) );
		}

		if ( $this->args['sent'] ) {
			$this->add_condition( '_sent', '=', $this->id_parse( $this->args['sent'] ) );
		}

		if ( $this->args['sent__not_in'] ) {
			$this->add_condition( '_sent__not_in', '=', $this->id_parse( $this->args['sent__not_in'] ) );
		}

		if ( $this->args['sent_before'] ) {
			$this->add_condition( '_sent_before', '=', $this->get_timestamp( $this->args['sent_before'] ) );
		}

		if ( $this->args['sent_after'] ) {
			$this->add_condition( '_sent_after', '=', $this->get_timestamp( $this->args['sent_after'] ) );
		}

		if ( $this->args['open'] ) {
			$this->add_condition( '_open', '=', $this->id_parse( $this->args['open'] ) );
		}

		if ( $this->args['open__not_in'] ) {
			$this->add_condition( '_open__not_in', '=', $this->id_parse( $this->args['open__not_in'] ) );
		}

		if ( $this->args['open_before'] ) {
			$this->add_condition( '_open_before', '=', $this->get_timestamp( $this->args['open_before'] ) );
		}

		if ( $this->args['open_after'] ) {
			$this->add_condition( '_open_after', '=', $this->get_timestamp( $this->args['open_after'] ) );
		}

		if ( $this->args['click'] ) {
			$this->add_condition( '_click', '=', $this->id_parse( $this->args['click'] ) );
		}

		if ( $this->args['click__not_in'] ) {
			$this->add_condition( '_click__not_in', '=', $this->id_parse( $this->args['click__not_in'] ) );
		}

		if ( $this->args['click_before'] ) {
			$this->add_condition( '_click_before', '=', $this->get_timestamp( $this->args['click_before'] ) );
		}

		if ( $this->args['click_after'] ) {
			$this->add_condition( '_click_after', '=', $this->get_timestamp( $this->args['click_after'] ) );
		}

		if ( $this->args['click_link'] ) {
			$this->add_condition( '_click_link', '=', ( $this->args['click_link'] ) );
		}

		if ( $this->args['click_link__not_in'] ) {
			$this->add_condition( '_click_link__not_in', '=', ( $this->args['click_link__not_in'] ) );
		}

		if ( $this->args['lists__not_in'] ) {
			$this->add_condition( '_lists__not_in', '=', ( $this->args['lists__not_in'] ) );
		}

		if ( ! $this->args['return_count'] ) {
			if ( ! empty( $this->args['fields'] ) ) {
				foreach ( $this->args['fields'] as $field ) {
					if ( 'fullname' == $field ) {
						$this->args['fields'][] = 'firstname';
						$this->args['fields'][] = 'lastname';
						$this->args['select'][] = ( ! $name_order ? "CONCAT_WS(' ', `field_firstname`.meta_value, `field_lastname`.meta_value)" : "CONCAT_WS(' ', `field_lastname`.meta_value, `field_firstname`.meta_value)" ) . ' AS fullname';
					} elseif ( in_array( strtolower( $field ), $this->fields ) ) {
						$this->args['select'][] = "subscribers.$field";
					} else {
						$this->args['select'][] = "`field_$field`.meta_value AS `$field`";
					}
				}
				$this->args['fields'] = array_unique( $this->args['fields'] );
				// sort($this->args['fields']);
			}
			if ( ! empty( $this->args['meta'] ) ) {
				foreach ( $this->args['meta'] as $field ) {
					if ( 'lat' == $field ) {
						$this->args['select'][] = "CAST(SUBSTRING_INDEX(`meta_lat`.meta_value, ',', 1) AS DECIMAL(10,4)) AS `lat`";
					} elseif ( 'lng' == $field ) {
						$this->args['select'][] = "CAST(SUBSTRING_INDEX(`meta_lng`.meta_value, ',', -1) AS DECIMAL(10,4)) AS `lng`";
					} else {
						$this->args['select'][] = "`meta_$field`.meta_value AS `$field`";
					}
				}
				// sort($this->args['meta']);
			}
		}

		$this->args = apply_filters( 'mailster_subscriber_query_args', $this->args );

		$cache_key = 'query_' . md5( serialize( $this->args ) );

		if ( $result = mailster_cache_get( $cache_key ) ) {
			return $result;
		}

		// if ( $this->args['lists'] !== false || $this->args['lists__not_in'] ) {
		// $join = "LEFT JOIN {$wpdb->prefix}mailster_lists_subscribers AS lists_subscribers ON subscribers.ID = lists_subscribers.subscriber_id AND lists_subscribers.added != 0";
		// if ( $this->args['lists__not_in'] && $this->args['lists__not_in'][0] != -1 ) {
		// $join .= ' AND lists_subscribers.list_id IN (' . implode( ',', array_filter( $this->args['lists__not_in'], 'is_numeric' ) ) . ')';
		// }
		// $joins[] = $join;
		// }
		if ( $this->args['lists'] !== false ) {
			$join = "LEFT JOIN {$wpdb->prefix}mailster_lists_subscribers AS lists_subscribers ON subscribers.ID = lists_subscribers.subscriber_id AND lists_subscribers.added != 0";
			$joins[] = $join;
		}

		if ( $this->args['queue'] || $this->args['queue__not_in'] ) {
			$join = "LEFT JOIN {$wpdb->prefix}mailster_queue AS queue ON subscribers.ID = queue.subscriber_id";
			if ( $this->args['queue'] && $this->args['queue'][0] != -1 ) {
				$join .= ' AND queue.campaign_id IN (' . implode( ',', array_filter( $this->args['queue'], 'is_numeric' ) ) . ')';
			}
			if ( $this->args['queue__not_in'] && $this->args['queue__not_in'][0] != -1 ) {
				$join .= ' AND queue.campaign_id IN (' . implode( ',', array_filter( $this->args['queue__not_in'], 'is_numeric' ) ) . ')';
			}
			$joins[] = $join;
		}

		if ( $this->args['unsubscribe'] || $this->args['unsubscribe__not_in'] ) {
			$join = "LEFT JOIN {$wpdb->prefix}mailster_actions AS actions_unsubscribe ON actions_unsubscribe.type = 4 AND subscribers.ID = actions_unsubscribe.subscriber_id";
			if ( $this->args['unsubscribe'] && $this->args['unsubscribe'][0] != -1 ) {
				$join .= ' AND actions_unsubscribe.campaign_id IN (' . implode( ',', array_filter( $this->args['unsubscribe'], 'is_numeric' ) ) . ')';
			}
			if ( $this->args['unsubscribe__not_in'] && $this->args['unsubscribe__not_in'][0] != -1 ) {
				$join .= ' AND actions_unsubscribe.campaign_id IN (' . implode( ',', array_filter( $this->args['unsubscribe__not_in'], 'is_numeric' ) ) . ')';
			}
			$joins[] = $join;
		}

		$meta_and_fields = wp_parse_args( $this->args['fields'], $this->args['meta'] );

		if ( $this->args['s'] ) {
			$search_fields = $this->args['search_fields'] ? $this->args['search_fields'] : array_merge( array( 'email', 'hash', 'fullname' ), $this->custom_fields );
			$meta_and_fields = array_merge( $search_fields, $meta_and_fields );
		}

		if ( in_array( 'fullname', $meta_and_fields ) ) {
			$meta_and_fields[] = 'firstname';
			$meta_and_fields[] = 'lastname';
		}

		if ( ! empty( $meta_and_fields ) ) {

			foreach ( $meta_and_fields as $field ) {

				$field = esc_sql( $field );

				if ( in_array( $field, array( 'lat', 'lng' ) ) ) {

					$joins[] = "LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS `meta_$field` ON `meta_$field`.subscriber_id = subscribers.ID AND `meta_$field`.meta_key = 'coords'";

				} elseif ( in_array( $field, $this->custom_fields ) ) {

					$joins[] = "LEFT JOIN {$wpdb->prefix}mailster_subscriber_fields AS `field_$field` ON `field_$field`.subscriber_id = subscribers.ID AND `field_$field`.meta_key = '$field'";

				} elseif ( in_array( $field, $this->wp_user_meta ) ) {
					$joins[] = "LEFT JOIN {$wpdb->usermeta} AS `meta_wp_$field` ON `meta_wp_$field`.user_id = subscribers.wp_id AND `meta_wp_$field`.meta_key = '" . str_replace( 'wp_', $wpdb->prefix, $field ) . "'";

				} elseif ( in_array( $field, $this->meta_fields ) ) {

					$joins[] = "LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS `meta_$field` ON `meta_$field`.subscriber_id = subscribers.ID AND `meta_$field`.meta_key = '$field'";
				}
			}
		}

		if ( $this->args['conditions'] ) {

			$cond = array();

			foreach ( $this->args['conditions'] as $i => $and_conditions ) {

				foreach ( $and_conditions as $j => $condition ) {

					$field = isset( $condition['field'] ) ? $condition['field'] : $condition[0];
					$operator = isset( $condition['operator'] ) ? $condition['operator'] : $condition[1];
					$value = isset( $condition['value'] ) ? $condition['value'] : $condition[2];
					// requires campaign to be sent
					if ( in_array( $field, array( '_open__not_in', '_click__not_in' ) ) ) {
						$this->add_condition( '_sent', '=', $value );
					}
				}
			}

			foreach ( $this->args['conditions'] as $i => $and_conditions ) {

				$sub_cond = array();

				foreach ( $and_conditions as $j => $condition ) {

					$field = isset( $condition['field'] ) ? $condition['field'] : $condition[0];
					$operator = isset( $condition['operator'] ) ? $condition['operator'] : $condition[1];
					$value = isset( $condition['value'] ) ? $condition['value'] : $condition[2];

					if ( in_array( $field, array( 'lat', 'lng' ) ) ) {

						$joins[] = "LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS `meta_coords` ON `meta_coords`.subscriber_id = subscribers.ID AND `meta_coords`.meta_key = 'coords'";

					} elseif ( in_array( $field, $this->custom_fields ) ) {

						$joins[] = "LEFT JOIN {$wpdb->prefix}mailster_subscriber_fields AS `field_$field` ON `field_$field`.subscriber_id = subscribers.ID AND `field_$field`.meta_key = '$field'";

					} elseif ( in_array( $field, $this->wp_user_meta ) ) {
						$joins[] = "LEFT JOIN {$wpdb->usermeta} AS `meta_wp_$field` ON `meta_wp_$field`.user_id = subscribers.wp_id AND `meta_wp_$field`.meta_key = '" . str_replace( 'wp_', $wpdb->prefix, $field ) . "'";

					} elseif ( in_array( $field, $this->meta_fields ) ) {

						$joins[] = "LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS `meta_$field` ON `meta_$field`.subscriber_id = subscribers.ID AND `meta_$field`.meta_key = '$field'";

					}

					if ( ! in_array( $field, $this->action_fields ) ) {

						$sub_cond[] = $this->get_condition( $field, $operator, $value );

					} else {

						if ( ! is_array( $value ) ) {
							$value = explode( ',', $value );
						}
						if ( false !== ($pos = array_search( '_last_5', $value ) ) ) {
							unset( $value[ $pos ] );
							$value = array_merge( $value, array( -1 ), mailster( 'campaigns' )->get_finished(array(
								'posts_per_page' => 5,
								'fields' => 'ids',
							)));
						}
						if ( false !== ($pos = array_search( '_last_7day', $value ) ) ) {
							unset( $value[ $pos ] );
							$value = array_merge( $value, array( -1 ), mailster( 'campaigns' )->get_finished(array(
								'meta_key' => '_mailster_finished',
								'meta_compare' => '>',
								'meta_value' => strtotime( '-7 days' ),
								'fields' => 'ids',
							)));

						}
						if ( false !== ($pos = array_search( '_last_1month', $value ) ) ) {
							unset( $value[ $pos ] );
							$value = array_merge( $value, array( -1 ), mailster( 'campaigns' )->get_finished(array(
								'meta_key' => '_mailster_finished',
								'meta_compare' => '>',
								'meta_value' => strtotime( '-1 month' ),
								'fields' => 'ids',
							)));
						}
						if ( false !== ($pos = array_search( '_last_3month', $value ) ) ) {
							unset( $value[ $pos ] );
							$value = array_merge( $value, array( -1 ), mailster( 'campaigns' )->get_finished(array(
								'meta_key' => '_mailster_finished',
								'meta_compare' => '>',
								'meta_value' => strtotime( '-3 month' ),
								'fields' => 'ids',
							)));
						}
						$alias = 'actions' . $field . '_' . $i . '_' . $j;
						$value = array_unique( $value );

						if ( $field == '_lists__not_in' ) {

							$sub_cond[] = "subscribers.ID NOT IN ( SELECT subscriber_id FROM {$wpdb->prefix}mailster_lists_subscribers WHERE list_id IN (" . implode( ',', array_filter( $value, 'is_numeric' ) ) . ') )';

						} elseif ( 0 === strpos( $field, '_sent' ) ) {

							$join = "LEFT JOIN {$wpdb->prefix}mailster_actions AS $alias ON $alias.type = 1 AND subscribers.ID = $alias.subscriber_id";
							if ( ('_sent' == $field || '_sent__not_in' == $field) && $value && $value != -1 ) {
								$join .= " AND $alias.campaign_id IN (" . implode( ',', array_filter( $value, 'is_numeric' ) ) . ')';
							}

							if ( '_sent' === $field ) {
								$sub_cond[] = "$alias.subscriber_id IS NOT NULL";
							} elseif ( '_sent__not_in' === $field ) {
								$sub_cond[] = "$alias.subscriber_id IS NULL";
							} elseif ( '_sent_before' === $field ) {
								$sub_cond[] = "$alias.timestamp <= " . $this->get_timestamp( $value );
							} elseif ( '_sent_after' === $field ) {
								$sub_cond[] = "$alias.timestamp >= " . $this->get_timestamp( $value );
							}

							$joins[] = $join;

						} elseif ( 0 === strpos( $field, '_open' ) ) {

							$join = "LEFT JOIN {$wpdb->prefix}mailster_actions AS $alias ON $alias.type = 2 AND subscribers.ID = $alias.subscriber_id";
							if ( ('_open' === $field || '_open__not_in' === $field) && $value && $value != -1 ) {
								$join .= " AND $alias.campaign_id IN (" . implode( ',', array_filter( $value, 'is_numeric' ) ) . ')';
							}

							if ( '_open' === $field ) {
								$sub_cond[] = "$alias.subscriber_id IS NOT NULL";
							} elseif ( '_open__not_in' === $field ) {
								$sub_cond[] = "$alias.subscriber_id IS NULL";
							} elseif ( '_open_before' === $field ) {
								$sub_cond[] = "$alias.timestamp <= " . $this->get_timestamp( $value );
							} elseif ( '_open_after' === $field ) {
								$sub_cond[] = "$alias.timestamp >= " . $this->get_timestamp( $value );
							}

							$joins[] = $join;

						} elseif ( 0 === strpos( $field, '_click' ) ) {

							$join = "LEFT JOIN {$wpdb->prefix}mailster_actions AS $alias ON $alias.type = 3 AND subscribers.ID = $alias.subscriber_id";
							if ( ('_click' === $field || '_click__not_in' === $field) && $value && $value != -1 ) {
								$join .= " AND $alias.campaign_id IN (" . implode( ',', array_filter( $value, 'is_numeric' ) ) . ')';
							}
							$joins[] = $join;

							if ( '_click' === $field ) {
								$sub_cond[] = "$alias.subscriber_id IS NOT NULL";
							} elseif ( '_click__not_in' === $field ) {
								$sub_cond[] = "$alias.subscriber_id IS NULL";
							} elseif ( '_click_before' === $field ) {
								$sub_cond[] = "$alias.timestamp <= " . $this->get_timestamp( $value );
							} elseif ( '_click_after' === $field ) {
								$sub_cond[] = "$alias.timestamp >= " . $this->get_timestamp( $value );
							} elseif ( '_click_link' === $field ) {
								$joins[] = "LEFT JOIN {$wpdb->prefix}mailster_links AS $field ON $alias.link_id = $field.ID AND $field.link IN ('" . implode( "','", $value ) . "')";
								$sub_cond[] = "$alias.subscriber_id IS NOT NULL";
							} elseif ( '_click_link__not_in' === $field ) {
								$joins[] = "LEFT JOIN {$wpdb->prefix}mailster_links AS $field ON $alias.link_id = $field.ID AND $field.link IN ('" . implode( "','", $value ) . "')";
								$sub_cond[] = "$alias.link_id IS NULL";
							}
						}
					}
				}
				$sub_cond = array_filter( $sub_cond );
				if ( ! empty( $sub_cond ) ) {
					$cond[] = '( ' . implode( ' OR ', $sub_cond ) . ' )';
				}
			}
			if ( ! empty( $cond ) ) {
				$wheres[] = 'AND ( ' . implode( ' AND ', $cond ) . ' )';
			}
		}

		if ( $this->args['lists'] !== false && $this->args['lists'] !== true ) {
			// unassigned members if NULL
			if ( is_array( $this->args['lists'] ) ) {
				$this->args['lists'] = array_filter( $this->args['lists'], 'is_numeric' );
			}

			$wheres[] = (is_null( $this->args['lists'] ) ) ? 'AND lists_subscribers.list_id IS NULL' : ( empty( $this->args['lists'] ) ? 'AND lists_subscribers.list_id = 0' : 'AND lists_subscribers.list_id IN (' . implode( ',', $this->args['lists'] ) . ')' );
		}

		if ( $this->args['status'] !== false && ! is_null( $this->args['status'] ) ) {
			$wheres[] = 'AND subscribers.status IN (' . implode( ',', array_filter( $this->args['status'], 'is_numeric' ) ) . ')';
		}

		if ( $this->args['status__not_in'] !== false && ! is_null( $this->args['status__not_in'] ) ) {
			$wheres[] = 'AND subscribers.status NOT IN (' . implode( ',', array_filter( $this->args['status__not_in'], 'is_numeric' ) ) . ')';
		}

		if ( $this->args['include'] ) {
			$wheres[] = 'AND subscribers.ID IN (' . implode( ',', array_filter( $this->args['include'], 'is_numeric' ) ) . ')';
		}

		if ( $this->args['exclude'] ) {
			$wheres[] = 'AND subscribers.ID NOT IN (' . implode( ',', array_filter( $this->args['exclude'], 'is_numeric' ) ) . ')';
		}

		// if ( $this->args['lists__not_in'] ) {
		// $wheres[] = 'AND lists_subscribers.list_id IS NULL';
		// }
		if ( $this->args['unsubscribe'] ) {
			$wheres[] = 'AND actions_unsubscribe.subscriber_id IS NOT NULL';
		}

		if ( $this->args['unsubscribe__not_in'] ) {
			$wheres[] = 'AND actions_unsubscribe.subscriber_id IS NULL';
		}

		if ( $this->args['queue'] ) {
			$wheres[] = 'AND queue.subscriber_id IS NOT NULL';
		}

		if ( $this->args['queue__not_in'] ) {
			$wheres[] = 'AND queue.subscriber_id IS NULL';
		}

		if ( $this->args['s'] ) {

			$raw_search = addcslashes( trim( $this->args['s'] ), '%[]_' );
			$search_terms = array();
			$search_orders = array();
			$not_search_terms = array();
			$wildcard = $this->args['strict'] ? '' : '%';

			if ( $this->args['sentence'] ) {

				$search_terms = array( $raw_search );

			} else {

				if ( preg_match_all( '/("|\')(.+?)(\1)/', $raw_search, $quotes ) ) {
					$search_terms = array_merge( $search_terms, $quotes[2] );
					$raw_search = trim( str_replace( $quotes[0], '', $raw_search ) );
				}

				$search_terms = array_merge( $search_terms, explode( ' ', $raw_search ) );
				$search_terms = array_filter( $search_terms );

				$search_terms = str_replace( array( '*', '?' ), array( '%', '_' ), $search_terms );

				$not_search_terms = array_values( preg_grep( '/^-/', $search_terms ) );
				$search_terms = array_values( array_diff( $search_terms, $not_search_terms ) );
				$not_search_terms = preg_replace( '/^-/', '', $not_search_terms );
			}

			if ( ! empty( $search_terms ) ) {

				$search_terms = array_map( 'trim', $search_terms );
				$concated_search_terms = implode( ' ', $search_terms );

				foreach ( $search_terms as $i => $term ) {

					$searches = array();
					if ( empty( $term ) ) {
						continue;
					}

					$operator = 'OR';
					if ( ! $i || strpos( $term, '+' ) === 0 ) {
						$term = ltrim( $term, '+' );
						$operator = 'AND';
					}

					foreach ( $search_fields as $search_field ) {

						if ( 'fullname' == $search_field ) {
							if ( ! $name_order ) {
								$searches[] = "(CONCAT_WS(' ', `field_firstname`.meta_value, `field_lastname`.meta_value) LIKE '$wildcard$term$wildcard')";
							} else {
								$searches[] = "(CONCAT_WS(' ', `field_lastname`.meta_value, `field_firstname`.meta_value) LIKE '$wildcard$term$wildcard')";
							}
						} elseif ( in_array( $search_field, $this->custom_fields ) ) {

							$searches[] = "(`field_$search_field`.meta_value LIKE '$wildcard$term$wildcard')";

						} elseif ( in_array( $search_field, $this->wp_user_meta ) ) {

							$searches[] = "(`meta_wp_$search_field`.meta_value LIKE '$wildcard$term$wildcard')";

						} elseif ( in_array( $search_field, $this->meta_fields ) ) {

							$searches[] = "(`meta_$search_field`.meta_value LIKE '$wildcard$term$wildcard')";

						} else {

							$searches[] = "(subscribers.$search_field LIKE '$wildcard$term$wildcard')";

						}
					}

					$wheres[] = "$operator ( " . implode( "\n" . ' OR ', $searches ) . ' )';
				}

				foreach ( $search_fields as $search_field ) {

					if ( 'fullname' == $search_field ) {
						if ( ! $name_order ) {
							$search_orders[] = "WHEN (CONCAT_WS(' ', `field_firstname`.meta_value, `field_lastname`.meta_value) LIKE '%$concated_search_terms%') THEN 1";
						} else {
							$search_orders[] = "WHEN (CONCAT_WS(' ', `field_lastname`.meta_value, `field_firstname`.meta_value) LIKE '%$concated_search_terms%') THEN 1";
						}
					} elseif ( in_array( $search_field, $this->custom_fields ) ) {

						$search_orders[] = "WHEN (`field_$search_field`.meta_value LIKE '%$concated_search_terms%') THEN 3";

					} elseif ( in_array( $search_field, $this->wp_user_meta ) ) {

						$search_orders[] = "WHEN (`meta_wp_$search_field`.meta_value LIKE '%$concated_search_terms%') THEN 4";

					} elseif ( in_array( $search_field, $this->meta_fields ) ) {

						$search_orders[] = "WHEN (`meta_$search_field`.meta_value LIKE '%$concated_search_terms%') THEN 5";

					} else {

						$search_orders[] = "WHEN (subscribers.$search_field LIKE '%$concated_search_terms%') THEN 2";

					}
				}
			}

			if ( ! empty( $not_search_terms ) ) {

				$not_search_terms = array_map( 'trim', $not_search_terms );
				$searches = array();

				foreach ( $not_search_terms as $i => $term ) {
					if ( empty( $term ) ) {
						continue;
					}

					foreach ( $search_fields as $search_field ) {

						if ( 'fullname' == $search_field ) {
							if ( ! $name_order ) {
								$searches[] = "(CONCAT_WS(' ', `field_firstname`.meta_value, `field_lastname`.meta_value) NOT LIKE '$wildcard$term$wildcard' OR CONCAT_WS(' ', `field_firstname`.meta_value, `field_lastname`.meta_value) IS NULL)";
							} else {
								$searches[] = "(CONCAT_WS(' ', `field_lastname`.meta_value, `field_firstname`.meta_value) NOT LIKE '$wildcard$term$wildcard' OR CONCAT_WS(' ', `field_lastname`.meta_value, `field_firstname`.meta_value) IS NULL)";
							}
						} elseif ( in_array( $search_field, $this->custom_fields ) ) {

							$searches[] = "(`field_$search_field`.meta_value NOT LIKE '$wildcard$term$wildcard' OR `field_$search_field`.meta_value IS NULL)";

						} elseif ( in_array( $search_field, $this->wp_user_meta ) ) {

							$searches[] = "(`meta_wp_$search_field`.meta_value NOT LIKE '$wildcard$term$wildcard' OR `meta_wp_$search_field`.meta_value IS NULL)";

						} elseif ( in_array( $search_field, $this->meta_fields ) ) {

							$searches[] = "(`meta_$search_field`.meta_value NOT LIKE '$wildcard$term$wildcard' OR `meta_$search_field`.meta_value IS NULL)";

						} else {

							$searches[] = "(subscribers.$search_field NOT LIKE '$wildcard$term$wildcard')";

						}
					}
				}

				$wheres[] = 'AND ( ' . implode( "\n" . ' AND ', $searches ) . ' )';

			}
		}

		if ( $this->args['where'] ) {
			$wheres[] = 'AND ( ' . implode( ' AND ', array_unique( $this->args['where'] ) ) . " )\n";
		}

		if ( $this->args['orderby'] && ! $this->args['return_count'] ) {

			$ordering = isset( $this->args['order'][0] ) ? strtoupper( $this->args['order'][0] ) : 'ASC';
			$orders = array();
			if ( ! empty( $search_orders ) ) {
				$orders[] = '(CASE ' . implode( ' ', $search_orders ) . ' ELSE 10 END)';
			}
			foreach ( $this->args['orderby'] as $i => $orderby ) {
				$ordering = isset( $this->args['order'][ $i ] ) ? strtoupper( $this->args['order'][ $i ] ) : $ordering;
				if ( in_array( $orderby, $this->custom_fields ) ) {

					$orders[] = "`field_$orderby`.meta_value $ordering";

				} elseif ( in_array( $orderby, $this->wp_user_meta ) ) {

					$orders[] = "`meta_wp_$orderby`.meta_value $ordering";

				} elseif ( in_array( $orderby, $this->meta_fields ) ) {

					$orders[] = "`meta_$orderby`.meta_value $ordering";

				} elseif ( in_array( $orderby, $this->fields ) ) {

					$orders[] = "subscribers.$orderby $ordering";
				} else {

					$orders[] = "$orderby $ordering";
				}
			}
		}

		$select = 'SELECT';

		if ( $this->args['calc_found_rows'] ) {
			$select .= ' SQL_CALC_FOUND_ROWS subscribers.ID,';
		}

		$select .= ' ' . implode( ', ', $this->args['select'] ) . "\n";

		$from = " FROM {$wpdb->prefix}mailster_subscribers AS subscribers" . "\n";

		$join = '';
		if ( ! empty( $joins ) ) {
			$join = ' ' . implode( "\n ", array_unique( $joins ) ) . "\n";
		}

		$where = '';
		if ( ! empty( $wheres ) ) {
			$where = ' WHERE 1=1 ' . implode( "\n  ", array_unique( $wheres ) ) . "\n";
		}

		$group = '';
		if ( ! $this->args['return_count'] ) {
			$group = ' GROUP BY subscribers.ID' . "\n";
		}

		$having = '';
		if ( $this->args['having'] ) {
			$having = ' HAVING ' . esc_sql( $this->args['having'] ) . "\n";
		}

		$order = '';
		if ( ! empty( $orders ) ) {
			$order = ' ORDER BY ' . implode( ', ', array_unique( $orders ) ) . "\n";
		}

		$limit = '';
		if ( $this->args['limit'] && ! $this->args['return_count'] ) {
			$limit = ' LIMIT ' . (int) $this->args['offset'] . ', ' . (int) $this->args['limit'];
		}

		$sql = $select . $from . $join . $where . $group . $having . $order . $limit;

		// legacy filter
		$sql = apply_filters( 'mailster_campaign_get_subscribers_by_list_sql', $sql );

		$sql = apply_filters( 'mailster_subscriber_query_sql', $sql );

		// error_log( $sql );
		if ( $this->args['return_sql'] ) {
			$result = $this->last_query = $sql;
			$this->last_error = null;
			$this->last_result = null;
		} else {
			if ( $this->args['return_ids'] ) {
				$result = $wpdb->get_col( $sql );
			} elseif ( $this->args['return_count'] ) {
				$result = $wpdb->get_var( $sql );
			} else {
				$result = $wpdb->get_results( $sql );
				// $result = $this->cast( $wpdb->get_results( $sql ) );
			}
			$this->last_query = $wpdb->last_query;
			$this->last_error = $wpdb->last_error;
			$this->last_result = $result;

			if ( $this->last_error && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( $this->last_error );
			}
		}

		mailster_cache_set( $cache_key, $result );

		return $result;

	}


	private function cast( $result ) {

		$className = 'MailsterSubscriber';
		$return = array();

		foreach ( $result as $key => $value ) {
			$return[] = unserialize(sprintf(
				'O:%d:"%s"%s',
				strlen( $className ),
				$className,
				strstr( strstr( serialize( $value ), '"' ), ':' )
			));
		}

		return $return;

	}


	private function get_condition( $field, $operator, $value ) {

		// sanitation
		$field = esc_sql( $field );
		$value = addslashes( stripslashes( $value ) );
		$operator = $this->get_field_operator( $operator );

		$is_empty = '' == $value;
		$extra = '';
		$positive = false;
		$f = false;

		// data sanitation
		switch ( $field ) {
			case 'rating':
				$value = str_replace( ',', '.', $value );
				if ( strpos( $value, '%' ) !== false || $value > 5 ) {
					$value = (float) $value / 100;
				} elseif ( $value >= 1 ) {
					$value = (float) $value * 0.2;
				}
				break;
			case 'lat':
				$f = "CAST(SUBSTRING_INDEX(`meta_coords`.meta_value, ',', 1) AS DECIMAL(10,4))";
				break;
			case 'lng':
				$f = "CAST(SUBSTRING_INDEX(`meta_coords`.meta_value, ',', -1) AS DECIMAL(10,4))";
				break;
			case 'geo':
				if ( '=' == $operator ) {
					return " (`meta_$field`.meta_value LIKE '$value|%' OR `meta_$field`.meta_value LIKE '%|$value') ";
				}
				if ( '!=' == $operator ) {
					return " (`meta_$field`.meta_value NOT LIKE '$value|%' AND `meta_$field`.meta_value NOT LIKE '%|$value') ";
				}
		}

		switch ( $operator ) {
			case '=':
			case 'is':
				$positive = true;
			case '!=':
			case 'is_not':

				if ( $f ) {
				} elseif ( in_array( $field, $this->custom_date_fields ) ) {
					$f = "STR_TO_DATE(`field_$field`.meta_value,'%Y-%m-%d')";
				} elseif ( in_array( $field, $this->time_fields ) ) {
					$f = "FROM_UNIXTIME(subscribers.$field, '%Y-%m-%d')";
					$value = $this->get_timestamp( $value, 'Y-m-d' );
				} elseif ( in_array( $field, $this->custom_fields ) ) {
					$f = "`field_$field`.meta_value";
				} elseif ( in_array( $field, $this->meta_fields ) ) {
					$f = "`meta_$field`.meta_value";
				} elseif ( in_array( $field, $this->wp_user_meta ) ) {
					$f = "`meta_wp_$field`.meta_value";
					if ( $field == 'wp_capabilities' ) {
						$value = 's:' . strlen( $value ) . ':"' . strtolower( addcslashes( $value, '_%\\' ) ) . '";';
						return "`meta_wp_$field`.meta_value " . ( in_array( $operator, array( 'is', '=' ) ) ? 'LIKE' : 'NOT LIKE' ) . " '%$value%'";
						break;
					}
				} else {
					$f = "subscribers.$field";
				}

				$c = $f . ' ' . ( $positive ? '=' : '!=' ) . " '$value'";
				if ( $is_empty && $positive || ! $positive ) {
					$c = '( ' . $c . ' OR ' . $f . ' IS NULL )';
				}

				return $c;
				break;

			case '<>':
			case 'contains':
				$positive = true;
			case '!<>':
			case 'contains_not':
				$value = addcslashes( $value, '_%\\' );
				if ( $field == 'wp_capabilities' ) {
					$value = "'a:%" . strtolower( $value ) . "%'";
				} else {
					$value = "'%$value%'";
				}
				if ( $f ) {
				} elseif ( in_array( $field, $this->custom_fields ) ) {
					$f = "`field_$field`.meta_value";
				} elseif ( in_array( $field, $this->meta_fields ) ) {
					$f = "`meta_$field`.meta_value";
				} elseif ( in_array( $field, $this->wp_user_meta ) ) {
					$f = "`meta_wp_$field`.meta_value";
				} else {
					$f = "subscribers.$field";
				}

				$c = $f . ' ' . ( $positive ? 'LIKE' : 'NOT LIKE' ) . " $value";
				if ( $is_empty && $positive || ! $positive ) {
					$c = '( ' . $c . ' OR ' . $f . ' IS NULL )';
				}

				return $c;
				break;

			case '^':
			case 'begin_with':
				$value = addcslashes( $value, '_%\\' );
				if ( $field == 'wp_capabilities' ) {
					$value = "'%\"" . strtolower( $value ) . "%'";
				} else {
					$value = "'$value%'";
				}
				if ( $f ) {
				} elseif ( in_array( $field, $this->custom_fields ) ) {
					$f = "`field_$field`.meta_value";
				} elseif ( in_array( $field, $this->meta_fields ) ) {
					$f = "`meta_$field`.meta_value";
				} elseif ( in_array( $field, $this->wp_user_meta ) ) {
					$f = "`meta_wp_$field`.meta_value";
				} else {
					$f = "subscribers.$field";
				}

				$c = $f . " LIKE $value";

				return $c;
				break;

			case '$':
			case 'end_with':
				$value = addcslashes( $value, '_%\\' );
				if ( $field == 'wp_capabilities' ) {
					$value = "'%" . strtolower( $value ) . "\"%'";
				} else {
					$value = "'%$value'";
				}

				if ( $f ) {
				} elseif ( in_array( $field, $this->custom_fields ) ) {
					$f = "`field_$field`.meta_value";
				} elseif ( in_array( $field, $this->meta_fields ) ) {
					$f = "`meta_$field`.meta_value";
				} elseif ( in_array( $field, $this->wp_user_meta ) ) {
					$f = "`meta_wp_$field`.meta_value";
				} else {
					$f = "subscribers.$field";
				}

				$c = $f . " LIKE $value";

				return $c;
				break;

			case '>=':
			case 'is_greater_equal':
			case '<=':
			case 'is_smaller_equal':
				$extra = '=';
			case '>':
			case 'is_greater':
			case '<':
			case 'is_smaller':

				if ( $f ) {
				} elseif ( in_array( $field, $this->custom_date_fields ) ) {
					$f = "STR_TO_DATE(`field_$field`.meta_value,'%Y-%m-%d')";
					$value = "'$value'";
				} elseif ( in_array( $field, $this->time_fields ) ) {
					$f = "FROM_UNIXTIME(subscribers.$field, '%Y-%m-%d')";
					$value = "'" . $this->get_timestamp( $value, 'Y-m-d' ) . "'";
				} elseif ( in_array( $field, $this->custom_fields ) ) {
					$f = "`field_$field`.meta_value";
					$value = is_numeric( $value ) ? (float) $value : "'$value'";
				} elseif ( in_array( $field, $this->meta_fields ) ) {
					$f = "`meta_$field`.meta_value";
					$value = is_numeric( $value ) ? (float) $value : "'$value'";
				} elseif ( in_array( $field, $this->wp_user_meta ) ) {
					$f = "`meta_wp_$field`.meta_value";
					if ( $field == 'wp_capabilities' ) {
						$value = "'NOTPOSSIBLE'";
					}
				} else {
					$f = "subscribers.$field";
					$value = (float) $value;
				}

				$c = $f . ' ' . ( in_array( $operator, array( 'is_greater', 'is_greater_equal', '>', '>=' ) ) ? '>' . $extra : '<' . $extra ) . " $value";

				return $c;
				break;

			case '%':
			case 'pattern':
				$positive = true;
			case '!%':
			case 'not_pattern':

				if ( $f ) {
				} elseif ( in_array( $field, $this->custom_date_fields ) ) {
					$f = "STR_TO_DATE(`field_$field`.meta_value,'%Y-%m-%d')";
				} elseif ( in_array( $field, $this->time_fields ) ) {
					$f = "FROM_UNIXTIME(subscribers.$field, '%Y-%m-%d')";
				} elseif ( in_array( $field, $this->custom_fields ) ) {
					$f = "`field_$field`.meta_value";
				} elseif ( in_array( $field, $this->meta_fields ) ) {
					$f = "`meta_$field`.meta_value";
				} elseif ( in_array( $field, $this->wp_user_meta ) ) {
					$f = "`meta_wp_$field`.meta_value";
				} else {
					$f = "subscribers.$field";
					if ( $field == 'wp_capabilities' ) {
						$value = "'NOTPOSSIBLE'";
						break;
					}
				}
				if ( $is_empty ) {
					$value = '.';
				}

				if ( ! $positive ) {
					$extra = 'NOT ';
				}

				$c = $f . ' ' . $extra . "REGEXP '$value'";
				if ( $is_empty && $positive || ! $positive ) {
					$c = '( ' . $c . ' OR ' . $f . ' IS NULL )';
				}

				return $c;
				break;

		}

	}

	private function get_field_operator( $operator ) {

		switch ( $operator ) {
			case '=':
				return 'is';
			case '!=':
				return 'is_not';
			case '<>':
				return 'contains';
			case '!<>':
				return 'contains_not';
			case '^':
				return 'begin_with';
			case '$':
				return 'end_with';
			case '>=':
				return 'is_greater_equal';
			case '<=':
				return 'is_smaller_equal';
			case '>':
				return 'is_greater';
			case '<':
				return 'is_smaller';
			case '%':
				return 'pattern';
			case '!%':
				return 'not_pattern';
		}

		return $operator;

	}

	private function get_custom_fields() {
		$custom_fields = mailster()->get_custom_fields( true );
		$custom_fields = wp_parse_args( array( 'firstname', 'lastname' ), (array) $custom_fields );

		return $custom_fields;
	}

	private function get_custom_date_fields() {
		$custom_date_fields = mailster()->get_custom_date_fields( true );

		return $custom_date_fields;
	}

	private function get_fields() {
		$fields = array( 'id', 'hash', 'email', 'wp_id', 'status', 'added', 'updated', 'signup', 'confirm', 'ip_signup', 'ip_confirm', 'rating' );

		return $fields;
	}

	private function get_time_fields() {
		$time_fields = array( 'added', 'updated', 'signup', 'confirm' );

		return $time_fields;
	}

	private function get_meta_fields() {
		$meta_fields = array( 'form', 'referer', 'client', 'clienttype', 'coords', 'geo', 'lang', 'timeoffset', 'lat', 'lng' );

		return $meta_fields;
	}

	private function get_wp_user_meta() {
		$wp_user_meta = wp_parse_args( array( 'wp_user_level', 'wp_capabilities' ), mailster( 'helper' )->get_wpuser_meta_fields() );
		// removing custom fields from wp user meta to prevent conflicts
		$wp_user_meta = array_diff( $wp_user_meta, array_merge( $this->fields, $this->custom_fields ) );

		return $wp_user_meta;
	}

	private function get_action_fields() {
		$action_fields = array( '_sent', '_sent__not_in', '_sent_before', '_sent_after', '_open', '_open__not_in', '_open_before', '_open_after', '_click', '_click__not_in', '_click_before', '_click_after', '_click_link', '_click_link__not_in', '_lists__not_in' );

		return $action_fields;
	}

	private function add_condition( $field, $operator, $value ) {
		$condition = array(
			'field' => $field,
			'operator' => $operator,
			'value' => $value,
		);

		if ( ! $this->args['conditions'] ) {
			$this->args['conditions'] = array();
		}

		array_unshift( $this->args['conditions'], array( $condition ) );

	}

	private function get_timestamp( $value, $format = null ) {
		$timestamp = is_numeric( $value ) ? strtotime( '@' . $value ) : strtotime( '' . $value );
		if ( false !== $timestamp ) {
		} elseif ( is_numeric( $value ) ) {
			$timestamp = (int) $value;
		} else {
			return false;
		}

		if ( is_null( $format ) ) {
			return $timestamp;
		}

		return date( $format, $timestamp );
	}

	private function id_parse( $ids ) {

		if ( empty( $ids ) ) {
			return $ids;
		}

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}

		$return = array();
		foreach ( $ids as $id ) {
			if ( is_numeric( $id ) ) {
				$return[] = $id;
			} elseif ( false !== strpos( $id, '-' ) ) {
				$splitted = explode( '-', $id );
				$min = min( $splitted );
				$max = max( $splitted );
				for ( $i = $min; $i <= $max; $i++ ) {
					$return[] = $i;
				}
			} else {
			}
		}

		return array_values( array_unique( $return ) );

	}


}

/**
 *
 */
class MailsterSubscriber {

	function __construct() {
	}
}
