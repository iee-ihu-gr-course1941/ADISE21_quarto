#!/bin/bash
gen_help=$'play.sh --help\nplay.sh --jq\nplay.sh --pp\nplay.sh --session --(help|create|read|read-one|remaining-pieces|set-next|end-game) ..args \nplay.sh --placement --(help|create|read)\nplay.sh --user --(help|login|logout|validate-token|sign-up)'

session_help=$'Login is required before using any session functions\n--create <no args>\n--end-game <session-id>\n--join <session-id>\n--read <no-args>\n--read-one <session-id>\n--remaining-pieces <session-id>\n--set-next <session-id> <piece-id>'

placement_help=$'Login is required before using any placement functions\n--create <session-id> <pos-x> <pos-y>\n--read <session-id>'

user_help=$'To let login set your id and access_token environment variables, you need to run it using your current shell instance via the "dot space script" syntax: ". ./play.sh"\n--sign-up <username> <password>\n--login <username> <password>\n--validate-token <no args>\nlogout <no args>'

case $1 in
  -h|--help)
    echo "$gen_help"
    ;;
  -s|--session)
    case $2 in
      -h|--help)
        echo "$session_help"
        ;;
      --state)
        if [[ -z $3 ]]; then
          echo 'one or more variables are undefined'
          exit 1
        fi
        ./scripts/print-info.mjs $(curl -f -s -d "{\"id\":\"$id\",\"access_token\":\"$access_token\"}" -H "Content-Type: application/json" -X GET https://users.it.teithe.gr/\~it185291/api/sessions/read_one.php\?session_id\=$3) \
          && ./scripts/print-board.mjs $(curl -s -f -d "{\"id\":\"$id\",\"access_token\":\"$access_token\"}" -H "Content-Type: application/json" -X GET https://users.it.teithe.gr/\~it185291/api/placements/read.php\?session_id\=$3)
        ;;
      --create)
        curl -f -d "{\"id\":\"$id\",\"access_token\":\"$access_token\"}" \
                -H "Content-Type: application/json" \
                -X POST https://users.it.teithe.gr/\~it185291/api/sessions/create.php
        ;;
      --read)
        curl -f -d "{\"id\":\"$id\",\"access_token\":\"$access_token\"}" \
                -H "Content-Type: application/json" \
                -X GET https://users.it.teithe.gr/\~it185291/api/sessions/read.php
        ;;
      --read-one)
        if [[ -z $3 ]]; then
          echo 'one or more variables are undefined'
          exit 1
        fi
        curl -f -d "{\"id\":\"$id\",\"access_token\":\"$access_token\"}" \
                -H "Content-Type: application/json" \
                -X GET https://users.it.teithe.gr/\~it185291/api/sessions/read_one.php\?session_id\=$3
        ;;
      --end-game)
        if [[ -z $3 ]]; then
          echo 'one or more variables are undefined'
          exit 1
        fi
        curl -f -d "{\"id\":\"$id\",\"access_token\":\"$access_token\"}" \
                -H "Content-Type: application/json" \
                -X DELETE https://users.it.teithe.gr/\~it185291/api/sessions/end_game.php\?session_id\=$3
        ;;
      --set-next)
        if [[ -z $3 || -z $4 ]]; then
          echo 'one or more variables are undefined'
          exit 1
        fi
        curl -f -d "{\"id\":\"$id\",\"access_token\":\"$access_token\",\"next_piece_id\":$4}" \
                -H "Content-Type: application/json" \
                -X PUT https://users.it.teithe.gr/\~it185291/api/sessions/set_next.php\?session_id\=$3
        ;;
      --remaining-pieces)
        if [[ -z $3 ]]; then
          echo 'one or more variables are undefined'
          exit 1
        fi
        curl -f -d "{\"id\":\"$id\",\"access_token\":\"$access_token\"}" \
                -H "Content-Type: application/json" \
                -X GET https://users.it.teithe.gr/\~it185291/api/sessions/remaining_pieces.php\?session_id\=$3
        ;;
      --join)
        if [[ -z $3 ]]; then
          echo 'one or more variables are undefined'
          exit 1
        fi
        curl -f -d "{\"id\":\"$id\",\"access_token\":\"$access_token\"}" \
                -H "Content-Type: application/json" \
                -X PUT https://users.it.teithe.gr/\~it185291/api/sessions/join.php\?session_id\=$3
        ;;
    esac
    ;;
  -p|--placement)
    case $2 in
      -h|--help)
        echo "$placement_help"
        ;;
      --create)
        if [[ -z $3 || -z $4 || -z $5 ]]; then
          echo 'one or more variables are undefined'
          exit 1
        fi
        curl  -f -d "{\"id\":\"$id\",\"access_token\":\"$access_token\",\"pos_x\":$4,\"pos_y\":$5}" \
                -H "Content-Type: application/json" \
                -X POST https://users.it.teithe.gr/\~it185291/api/placements/create.php\?session_id\=$3
        ;;
      --read)
        if [[ -z $3 ]]; then
          echo 'one or more variables are undefined'
          exit 1
        fi
        curl -f -d "{\"id\":\"$id\",\"access_token\":\"$access_token\"}" \
                -H "Content-Type: application/json" \
                -X GET https://users.it.teithe.gr/\~it185291/api/placements/read.php\?session_id\=$3
        ;;
    esac
    ;;
  -u|--user)
    case $2 in
      -h|--help)
        echo "$user_help"
        ;;
      --login)
        if [[ -z $3 || -z $4 ]]; then
          echo 'one or more variables are undefined'
          exit 1
        fi
        $(curl -f -d "{\"username\":\"$3\",\"password\":\"$4\"}" \
              -H "Content-Type: application/json" \
              -X GET https://users.it.teithe.gr/\~it185291/api/users/login.php | jq -r 'keys[] as $k | "export \($k)=\(.[$k])"')
        ;;
      --logout)
        curl -f -d "{\"id\":\"$id\",\"access_token\":\"$access_token\"}" \
                -H \"Content-Type: application/json\" \
                -X GET https://users.it.teithe.gr/\~it185291/api/users/logout.php
        eval $req
        ;;
      --sign-up)
        if [[ -z $3 || -z $4 ]]; then
          echo 'one or more variables are undefined'
          exit 1
        fi
        curl -f -d "{\"username\":\"$3\",\"password\":\"$4\"}" \
                -H \"Content-Type: application/json\" \
                -X POST https://users.it.teithe.gr/\~it185291/api/users/sign_up.php
        ;;
      --validate-token)
        curl -f -d "{\"id\":\"$id\",\"access_token\":\"$access_token\"}" \
                -H "Content-Type: application/json" \
                -X GET https://users.it.teithe.gr/\~it185291/api/users/validate_token.php
        ;;
    esac
    ;;
esac
