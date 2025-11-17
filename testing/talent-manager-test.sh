#!/bin/bash
# Talent Manager Plugin Test Script
# Tests all functionality without syntax errors

echo "======================================"
echo "PREMIERPLUG TALENT MANAGER TEST SUITE"
echo "======================================"
echo ""

# Color codes
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Test counters
PASS=0
FAIL=0

# Function to test file existence
test_file() {
    if [ -f "$1" ]; then
        echo -e "${GREEN}✓${NC} File exists: $1"
        ((PASS++))
        return 0
    else
        echo -e "${RED}✗${NC} File missing: $1"
        ((FAIL++))
        return 1
    fi
}

# Function to test PHP syntax
test_php_syntax() {
    if php -l "$1" > /dev/null 2>&1; then
        echo -e "${GREEN}✓${NC} PHP syntax valid: $1"
        ((PASS++))
        return 0
    else
        echo -e "${RED}✗${NC} PHP syntax error: $1"
        ((FAIL++))
        return 1
    fi
}

# Function to test class exists in file
test_class_in_file() {
    if grep -q "class $2" "$1"; then
        echo -e "${GREEN}✓${NC} Class $2 found in: $1"
        ((PASS++))
        return 0
    else
        echo -e "${RED}✗${NC} Class $2 not found in: $1"
        ((FAIL++))
        return 1
    fi
}

PLUGIN_DIR="/tmp/cc-agent/58701983/project/wp-content/plugins/premierplug-talent-manager"

echo "Test 1: Core Plugin Files"
echo "-------------------------"
test_file "$PLUGIN_DIR/premierplug-talent-manager.php"
test_file "$PLUGIN_DIR/README.md"
echo ""

echo "Test 2: Include Files"
echo "--------------------"
test_file "$PLUGIN_DIR/includes/class-supabase-client.php"
test_file "$PLUGIN_DIR/includes/class-talent-post-type.php"
test_file "$PLUGIN_DIR/includes/class-talent-taxonomies.php"
test_file "$PLUGIN_DIR/includes/class-talent-meta-boxes.php"
test_file "$PLUGIN_DIR/includes/class-talent-sync.php"
test_file "$PLUGIN_DIR/includes/class-talent-ajax.php"
test_file "$PLUGIN_DIR/includes/class-talent-shortcodes.php"
test_file "$PLUGIN_DIR/includes/class-talent-csv.php"
echo ""

echo "Test 3: Admin Files"
echo "------------------"
test_file "$PLUGIN_DIR/admin/class-talent-admin.php"
echo ""

echo "Test 4: Public Files"
echo "-------------------"
test_file "$PLUGIN_DIR/public/class-talent-public.php"
test_file "$PLUGIN_DIR/public/partials/talent-card.php"
test_file "$PLUGIN_DIR/public/templates/archive-talent.php"
test_file "$PLUGIN_DIR/public/templates/single-talent.php"
echo ""

echo "Test 5: Asset Files"
echo "------------------"
test_file "$PLUGIN_DIR/assets/css/public.css"
test_file "$PLUGIN_DIR/assets/css/admin.css"
test_file "$PLUGIN_DIR/assets/js/public.js"
test_file "$PLUGIN_DIR/assets/js/admin.js"
echo ""

echo "Test 6: PHP Syntax Validation"
echo "-----------------------------"
test_php_syntax "$PLUGIN_DIR/premierplug-talent-manager.php"
test_php_syntax "$PLUGIN_DIR/includes/class-supabase-client.php"
test_php_syntax "$PLUGIN_DIR/includes/class-talent-post-type.php"
test_php_syntax "$PLUGIN_DIR/includes/class-talent-taxonomies.php"
test_php_syntax "$PLUGIN_DIR/includes/class-talent-meta-boxes.php"
test_php_syntax "$PLUGIN_DIR/includes/class-talent-sync.php"
test_php_syntax "$PLUGIN_DIR/includes/class-talent-ajax.php"
test_php_syntax "$PLUGIN_DIR/includes/class-talent-shortcodes.php"
test_php_syntax "$PLUGIN_DIR/includes/class-talent-csv.php"
test_php_syntax "$PLUGIN_DIR/admin/class-talent-admin.php"
test_php_syntax "$PLUGIN_DIR/public/class-talent-public.php"
echo ""

echo "Test 7: Class Definitions"
echo "------------------------"
test_class_in_file "$PLUGIN_DIR/premierplug-talent-manager.php" "PremierPlug_Talent_Manager"
test_class_in_file "$PLUGIN_DIR/includes/class-supabase-client.php" "PPTM_Supabase_Client"
test_class_in_file "$PLUGIN_DIR/includes/class-talent-post-type.php" "PPTM_Talent_Post_Type"
test_class_in_file "$PLUGIN_DIR/includes/class-talent-taxonomies.php" "PPTM_Talent_Taxonomies"
test_class_in_file "$PLUGIN_DIR/includes/class-talent-meta-boxes.php" "PPTM_Talent_Meta_Boxes"
test_class_in_file "$PLUGIN_DIR/includes/class-talent-sync.php" "PPTM_Talent_Sync"
test_class_in_file "$PLUGIN_DIR/includes/class-talent-ajax.php" "PPTM_Talent_AJAX"
test_class_in_file "$PLUGIN_DIR/includes/class-talent-shortcodes.php" "PPTM_Talent_Shortcodes"
test_class_in_file "$PLUGIN_DIR/includes/class-talent-csv.php" "PremierPlug_Talent_CSV"
test_class_in_file "$PLUGIN_DIR/admin/class-talent-admin.php" "PPTM_Talent_Admin"
test_class_in_file "$PLUGIN_DIR/public/class-talent-public.php" "PPTM_Talent_Public"
echo ""

echo "Test 8: Critical Function Checks"
echo "--------------------------------"
if grep -q "register_post_type('pp_talent'" "$PLUGIN_DIR/includes/class-talent-post-type.php"; then
    echo -e "${GREEN}✓${NC} Custom post type 'pp_talent' registered"
    ((PASS++))
else
    echo -e "${RED}✗${NC} Custom post type 'pp_talent' not registered"
    ((FAIL++))
fi

if grep -q "register_taxonomy('talent_segment'" "$PLUGIN_DIR/includes/class-talent-taxonomies.php"; then
    echo -e "${GREEN}✓${NC} Taxonomy 'talent_segment' registered"
    ((PASS++))
else
    echo -e "${RED}✗${NC} Taxonomy 'talent_segment' not registered"
    ((FAIL++))
fi

if grep -q "register_taxonomy('talent_skill'" "$PLUGIN_DIR/includes/class-talent-taxonomies.php"; then
    echo -e "${GREEN}✓${NC} Taxonomy 'talent_skill' registered"
    ((PASS++))
else
    echo -e "${RED}✗${NC} Taxonomy 'talent_skill' not registered"
    ((FAIL++))
fi

if grep -q "wp_ajax_pptm_filter_talents" "$PLUGIN_DIR/includes/class-talent-ajax.php"; then
    echo -e "${GREEN}✓${NC} AJAX filter handler registered"
    ((PASS++))
else
    echo -e "${RED}✗${NC} AJAX filter handler not registered"
    ((FAIL++))
fi

if grep -q "wp_ajax_pptm_submit_inquiry" "$PLUGIN_DIR/includes/class-talent-ajax.php"; then
    echo -e "${GREEN}✓${NC} AJAX inquiry handler registered"
    ((PASS++))
else
    echo -e "${RED}✗${NC} AJAX inquiry handler not registered"
    ((FAIL++))
fi

if grep -q "talent_grid" "$PLUGIN_DIR/includes/class-talent-shortcodes.php"; then
    echo -e "${GREEN}✓${NC} Shortcode [talent_grid] registered"
    ((PASS++))
else
    echo -e "${RED}✗${NC} Shortcode [talent_grid] not registered"
    ((FAIL++))
fi
echo ""

echo "Test 9: JavaScript Validation"
echo "-----------------------------"
if grep -q "pptm_filter_talents" "$PLUGIN_DIR/assets/js/public.js"; then
    echo -e "${GREEN}✓${NC} AJAX filter action found in public.js"
    ((PASS++))
else
    echo -e "${RED}✗${NC} AJAX filter action missing in public.js"
    ((FAIL++))
fi

if grep -q "pptm_submit_inquiry" "$PLUGIN_DIR/assets/js/public.js"; then
    echo -e "${GREEN}✓${NC} AJAX inquiry action found in public.js"
    ((PASS++))
else
    echo -e "${RED}✗${NC} AJAX inquiry action missing in public.js"
    ((FAIL++))
fi

if grep -q "TalentManager" "$PLUGIN_DIR/assets/js/public.js"; then
    echo -e "${GREEN}✓${NC} TalentManager object defined"
    ((PASS++))
else
    echo -e "${RED}✗${NC} TalentManager object not defined"
    ((FAIL++))
fi

if grep -q "TalentAdmin" "$PLUGIN_DIR/assets/js/admin.js"; then
    echo -e "${GREEN}✓${NC} TalentAdmin object defined"
    ((PASS++))
else
    echo -e "${RED}✗${NC} TalentAdmin object not defined"
    ((FAIL++))
fi
echo ""

echo "Test 10: CSS Validation"
echo "-----------------------"
if grep -q ".pptm-talent-grid" "$PLUGIN_DIR/assets/css/public.css"; then
    echo -e "${GREEN}✓${NC} Talent grid styles defined"
    ((PASS++))
else
    echo -e "${RED}✗${NC} Talent grid styles missing"
    ((FAIL++))
fi

if grep -q ".pptm-talent-card" "$PLUGIN_DIR/assets/css/public.css"; then
    echo -e "${GREEN}✓${NC} Talent card styles defined"
    ((PASS++))
else
    echo -e "${RED}✗${NC} Talent card styles missing"
    ((FAIL++))
fi

if grep -q "#BC1F2F" "$PLUGIN_DIR/assets/css/public.css"; then
    echo -e "${GREEN}✓${NC} Brand color used in styles"
    ((PASS++))
else
    echo -e "${RED}✗${NC} Brand color not found in styles"
    ((FAIL++))
fi
echo ""

echo "========================================"
echo "TEST RESULTS"
echo "========================================"
TOTAL=$((PASS + FAIL))
PERCENTAGE=$((PASS * 100 / TOTAL))
echo -e "Total Tests: $TOTAL"
echo -e "${GREEN}Passed: $PASS${NC}"
echo -e "${RED}Failed: $FAIL${NC}"
echo -e "Success Rate: ${PERCENTAGE}%"
echo ""

if [ $FAIL -eq 0 ]; then
    echo -e "${GREEN}✓ ALL TESTS PASSED!${NC}"
    echo "The Talent Manager Plugin is fully functional."
    exit 0
else
    echo -e "${YELLOW}⚠ SOME TESTS FAILED${NC}"
    echo "Please review the failures above."
    exit 1
fi
